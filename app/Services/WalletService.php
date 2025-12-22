<?php

namespace App\Services;

use App\Models\User;
use App\Models\Task;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Exception;

class WalletService
{
    // =========================
    // Helpers bc math (tránh float)
    // =========================
    private function bc_add($a, $b, $scale = 2) { return bcadd((string)$a, (string)$b, $scale); }
    private function bc_sub($a, $b, $scale = 2) { return bcsub((string)$a, (string)$b, $scale); }
    private function bc_mul($a, $b, $scale = 2) { return bcmul((string)$a, (string)$b, $scale); }
    private function bc_div($a, $b, $scale = 4) { return bcdiv((string)$a, (string)$b, $scale); }

    /**
     * Trừ trực tiếp vào balance (available). (Giữ lại chức năng cũ)
     */
    public function charge(User $user, float $amount, string $description): void
    {
        DB::transaction(function () use ($user, $amount, $description) {

            $wallet = $user->wallet()->lockForUpdate()->first();

            if (!$wallet) {
                throw new Exception('User chưa có ví');
            }

            $amount = bcadd((string)$amount, '0', 2);

            if (bccomp((string)$wallet->balance, (string)$amount, 2) < 0) {
                throw new Exception('Số dư không đủ');
            }

            // Nếu bạn đang có method withdraw() ở Wallet model thì dùng:
            // $wallet->withdraw($amount, $description);

            // Nếu chưa có withdraw() hoặc muốn chắc chắn:
            $balanceBefore = (string)$wallet->balance;
            $heldBefore = (string)($wallet->held_balance ?? '0.00');

            $wallet->balance = $this->bc_sub($wallet->balance, $amount, 2);
            $wallet->save();

            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'withdraw',
                'amount' => $amount,
                'description' => $description,
                'balance_before' => $balanceBefore,
                'balance_after' => (string)$wallet->balance,
                'held_before' => $heldBefore,
                'held_after' => (string)($wallet->held_balance ?? '0.00'),
            ]);
        });
    }

    /**
     * Tính số tiền HOLD mặc định từ Task:
     * net = days * expected_costs * (1 - rate/100)
     */
    public function calcExpectedAmount(Task $task): string
    {
        $days = (string)($task->days ?? 0);
        $expected = (string)($task->expected_costs ?? 0);
        $rate = (string)($task->rate ?? 0);

        $gross = $this->bc_mul($days, $expected, 2);
        $discount = $this->bc_sub('1', $this->bc_div($rate, '100', 4), 4);
        $net = $this->bc_mul($gross, $discount, 2);

        if (bccomp($net, '0', 2) < 0) return '0.00';
        return $net;
    }

    /**
     * HOLD tiền theo Task: balance giảm, held_balance tăng
     */
    public function holdTask(Task $task, ?string $amount = null): WalletTransaction
    {
        return DB::transaction(function () use ($task, $amount) {

            $task = Task::whereKey($task->id)->lockForUpdate()->firstOrFail();

            // Idempotent: nếu đã hold thì trả transaction hold gần nhất
            if (!empty($task->hold_transaction_id) || $task->status === 'held') {
                $existing = WalletTransaction::where('ref_type', 'task')
                    ->where('ref_id', $task->id)
                    ->where('type', 'hold')
                    ->orderByDesc('id')
                    ->first();
                if ($existing) return $existing;
            }

            /** @var Wallet|null $wallet */
            $wallet = Wallet::where('user_id', $task->user_id)->lockForUpdate()->first();
            if (!$wallet) {
                throw new Exception('User chưa có ví');
            }

            $holdAmount = $amount ?? $this->calcExpectedAmount($task);
            $holdAmount = bcadd((string)$holdAmount, '0', 2);

            if (bccomp($holdAmount, '0', 2) <= 0) {
                throw ValidationException::withMessages(['amount' => 'Số tiền hold không hợp lệ.']);
            }

            if (bccomp((string)$wallet->balance, $holdAmount, 2) < 0) {
                throw ValidationException::withMessages(['balance' => 'Số dư không đủ để đăng ký dịch vụ.']);
            }

            $cycle = (int)($task->hold_cycle ?? 1);
            $idempotencyKey = "task:{$task->id}:hold:{$cycle}";

            $dup = WalletTransaction::where('wallet_id', $wallet->id)
                ->where('idempotency_key', $idempotencyKey)
                ->first();
            if ($dup) return $dup;

            $balanceBefore = (string)$wallet->balance;
            $heldBefore = (string)($wallet->held_balance ?? '0.00');

            $wallet->balance = $this->bc_sub($wallet->balance, $holdAmount, 2);
            $wallet->held_balance = $this->bc_add($wallet->held_balance ?? '0.00', $holdAmount, 2);
            $wallet->save();

            $tx = WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'hold',
                'amount' => $holdAmount,
                'description' => "Hold tiền cho task #{$task->id}",
                'ref_type' => 'task',
                'ref_id' => $task->id,
                'idempotency_key' => $idempotencyKey,
                'meta' => [
                    'days' => $task->days,
                    'expected_costs' => (string)$task->expected_costs,
                    'rate' => (string)$task->rate,
                ],
                'balance_before' => $balanceBefore,
                'balance_after' => (string)$wallet->balance,
                'held_before' => $heldBefore,
                'held_after' => (string)$wallet->held_balance,
            ]);

            // Update task fields (nếu bạn đã migrate các cột này)
            $task->price_expected = $holdAmount;
            $task->hold_transaction_id = $tx->id;
            $task->status = 'held';
            $task->save();

            return $tx;
        });
    }

    /**
     * RELEASE hold (hủy/từ chối): held_balance giảm, balance tăng
     */
    public function releaseTask(Task $task, string $reason = 'release'): WalletTransaction
    {
        return DB::transaction(function () use ($task, $reason) {

            $task = Task::whereKey($task->id)->lockForUpdate()->firstOrFail();

            if (empty($task->hold_transaction_id) || bccomp((string)$task->price_expected, '0', 2) <= 0) {
                throw ValidationException::withMessages(['task' => 'Task chưa hold tiền.']);
            }

            if (!empty($task->capture_transaction_id) || $task->status === 'accepted') {
                throw ValidationException::withMessages(['task' => 'Task đã nghiệm thu, không thể release.']);
            }

            $wallet = Wallet::where('user_id', $task->user_id)->lockForUpdate()->firstOrFail();

            $amount = bcadd((string)$task->price_expected, '0', 2);

            $cycle = (int)($task->hold_cycle ?? 1);
            $idempotencyKey = "task:{$task->id}:release:{$cycle}";
            
            $dup = WalletTransaction::where('wallet_id', $wallet->id)
                ->where('idempotency_key', $idempotencyKey)
                ->first();
            if ($dup) return $dup;

            if (bccomp((string)$wallet->held_balance, $amount, 2) < 0) {
                throw ValidationException::withMessages(['wallet' => 'Held balance không đủ để release (dữ liệu lệch).']);
            }

            $balanceBefore = (string)$wallet->balance;
            $heldBefore = (string)$wallet->held_balance;

            $wallet->held_balance = $this->bc_sub($wallet->held_balance, $amount, 2);
            $wallet->balance = $this->bc_add($wallet->balance, $amount, 2);
            $wallet->save();

            $tx = WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'release',
                'amount' => $amount,
                'description' => "Release tiền task #{$task->id} ({$reason})",
                'ref_type' => 'task',
                'ref_id' => $task->id,
                'idempotency_key' => $idempotencyKey,
                'meta' => ['reason' => $reason],
                'balance_before' => $balanceBefore,
                'balance_after' => (string)$wallet->balance,
                'held_before' => $heldBefore,
                'held_after' => (string)$wallet->held_balance,
            ]);

            $task->status = 'canceled';
            $task->save();

            // reset để có thể hold lại lần sau (chu kỳ mới)
            $task->paid = 0; // nếu bạn có cột paid
            $task->hold_transaction_id = null;
            $task->price_expected = 0;
            $task->status = 'registered'; // hoặc 'released'
            $task->hold_cycle = (int)($task->hold_cycle ?? 1) + 1;
            $task->save();


            return $tx;
        });
    }

    /**
     * CAPTURE khi nghiệm thu: trừ từ held_balance
     * - mặc định final = price_expected
     * - nếu final < expected: tự release phần dư về balance
     * - nếu final > expected: báo lỗi (bạn có thể làm hold thêm sau)
     */
    public function captureTask(Task $task, ?string $finalAmount = null): WalletTransaction
    {
        return DB::transaction(function () use ($task, $finalAmount) {

            $task = Task::whereKey($task->id)->lockForUpdate()->firstOrFail();

            if (empty($task->hold_transaction_id) || bccomp((string)$task->price_expected, '0', 2) <= 0) {
                throw ValidationException::withMessages(['task' => 'Task chưa hold tiền.']);
            }

            if (!empty($task->capture_transaction_id) || $task->status === 'accepted') {
                $existing = WalletTransaction::where('ref_type', 'task')
                    ->where('ref_id', $task->id)
                    ->where('type', 'capture')
                    ->orderByDesc('id')
                    ->first();
                if ($existing) return $existing;

                throw ValidationException::withMessages(['task' => 'Task đã capture trước đó.']);
            }

            $wallet = Wallet::where('user_id', $task->user_id)->lockForUpdate()->firstOrFail();

            $expected = bcadd((string)$task->price_expected, '0', 2);
            $final = bcadd((string)($finalAmount ?? $expected), '0', 2);

            if (bccomp($final, $expected, 2) > 0) {
                throw ValidationException::withMessages([
                    'final' => 'Số tiền nghiệm thu lớn hơn số đã hold. (Cần hold thêm hoặc yêu cầu nạp thêm.)'
                ]);
            }

            $idempotencyKey = "task:{$task->id}:capture";
            $dup = WalletTransaction::where('wallet_id', $wallet->id)
                ->where('idempotency_key', $idempotencyKey)
                ->first();
            if ($dup) return $dup;

            if (bccomp((string)$wallet->held_balance, $final, 2) < 0) {
                throw ValidationException::withMessages(['wallet' => 'Held balance không đủ để capture (dữ liệu lệch).']);
            }

            $balanceBefore = (string)$wallet->balance;
            $heldBefore = (string)$wallet->held_balance;

            $wallet->held_balance = $this->bc_sub($wallet->held_balance, $final, 2);
            $wallet->save();

            $tx = WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'capture',
                'amount' => $final,
                'description' => "Capture nghiệm thu task #{$task->id}",
                'ref_type' => 'task',
                'ref_id' => $task->id,
                'idempotency_key' => $idempotencyKey,
                'meta' => ['expected' => $expected, 'final' => $final],
                'balance_before' => $balanceBefore,
                'balance_after' => (string)$wallet->balance,
                'held_before' => $heldBefore,
                'held_after' => (string)$wallet->held_balance,
            ]);

            // release phần dư nếu final < expected
            if (bccomp($final, $expected, 2) < 0) {
                $diff = bcsub($expected, $final, 2);

                if (bccomp((string)$wallet->held_balance, $diff, 2) < 0) {
                    throw ValidationException::withMessages(['wallet' => 'Held balance không đủ để release phần dư (dữ liệu lệch).']);
                }

                $idk = "task:{$task->id}:release_diff";
                $dup2 = WalletTransaction::where('wallet_id', $wallet->id)
                    ->where('idempotency_key', $idk)
                    ->first();

                if (!$dup2) {
                    $balanceB = (string)$wallet->balance;
                    $heldB = (string)$wallet->held_balance;

                    $wallet->held_balance = $this->bc_sub($wallet->held_balance, $diff, 2);
                    $wallet->balance = $this->bc_add($wallet->balance, $diff, 2);
                    $wallet->save();

                    WalletTransaction::create([
                        'wallet_id' => $wallet->id,
                        'type' => 'release',
                        'amount' => $diff,
                        'description' => "Release phần dư task #{$task->id}",
                        'ref_type' => 'task',
                        'ref_id' => $task->id,
                        'idempotency_key' => $idk,
                        'meta' => ['reason' => 'diff_after_capture'],
                        'balance_before' => $balanceB,
                        'balance_after' => (string)$wallet->balance,
                        'held_before' => $heldB,
                        'held_after' => (string)$wallet->held_balance,
                    ]);
                }
            }

            $task->price_final = $final;
            $task->capture_transaction_id = $tx->id;
            $task->status = 'accepted';
            $task->save();

            return $tx;
        });
    }
}
