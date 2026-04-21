<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\BulkPersonalMail;

class DepositController extends Controller
{
    /**
     * Danh sách nạp tiền
     */
    public function index(Request $request)
    {
        $query = Deposit::with(['user', 'histories.admin'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $deposits = $query->paginate(15)->withQueryString();

        return view('admin.deposits.index', compact('deposits'));
    }

    /**
     * Cập nhật trạng thái: duyệt / từ chối / rollback
     */
    public function updateStatus(Request $request, Deposit $deposit)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'note'   => 'nullable|string|max:1000',
        ]);

        $mailPayload = null;

        DB::transaction(function () use ($request, $deposit, &$mailPayload) {

            // Reload & lock
            $deposit = Deposit::where('id', $deposit->id)
                ->lockForUpdate()
                ->first();

            $wallet = Wallet::firstOrCreate([
                'user_id' => $deposit->user_id
            ]);

            /* ======================
               DUYỆT
            ====================== */
            if ($request->action === 'approve') {

                if ($deposit->status !== 'approved') {

                    // cộng tiền
                    $wallet->increment('balance', $deposit->amount);

                    $wallet->transactions()->create([
                        'amount' => $deposit->amount,
                        'type' => 'deposit',
                        'description' => 'Duyệt nạp tiền #' . $deposit->id,
                    ]);

                    $deposit->update(['status' => 'approved']);
                }
            }

            /* ======================
               TỪ CHỐI / ROLLBACK
            ====================== */
            if ($request->action === 'reject') {

                // nếu đã duyệt → rollback
                if ($deposit->status === 'approved') {

                    if ($wallet->balance < $deposit->amount) {
                        throw new \Exception('Không đủ số dư để rollback');
                    }

                    $wallet->decrement('balance', $deposit->amount);

                    $wallet->transactions()->create([
                        'amount' => -$deposit->amount,
                        'type' => 'rollback',
                        'description' => 'Rollback nạp tiền #' . $deposit->id,
                    ]);
                }

                $deposit->update(['status' => 'rejected']);
            }

            // Lưu lịch sử
            $deposit->histories()->create([
                'admin_id' => auth()->id(),
                'action' => $request->action,
                'note' => $request->note,
            ]);

            $user = $deposit->user()->select('id', 'yourname', 'name', 'email')->first();
            if ($user && !empty($user->email)) {
                $statusText = $request->action === 'approve' ? 'ĐƯỢC DUYỆT' : 'BỊ TỪ CHỐI';
                $subject = $request->action === 'approve'
                    ? 'Thông báo: Lệnh nạp tiền đã được duyệt'
                    : 'Thông báo: Lệnh nạp tiền đã bị từ chối';

                $noteText = trim((string) $request->note);
                $content = "Lệnh nạp tiền của bạn vừa được cập nhật.\n"
                    . "Mã lệnh: " . ($deposit->transaction_code ?: ('#' . $deposit->id)) . "\n"
                    . "Số tiền: " . number_format((float) $deposit->amount, 0, ',', '.') . " VND\n"
                    . "Trạng thái: " . $statusText;

                if ($noteText !== '') {
                    $content .= "\nGhi chú: " . $noteText;
                }

                $mailPayload = [
                    'to' => $user->email,
                    'name' => $user->yourname ?: ($user->name ?: 'Bạn'),
                    'subject' => $subject,
                    'content' => $content,
                ];
            }
        });

        if ($mailPayload) {
            Mail::to($mailPayload['to'])->send(
                new BulkPersonalMail(
                    $mailPayload['name'],
                    $mailPayload['content'],
                    $mailPayload['subject']
                )
            );
        }

        return back()->with('success', 'Cập nhật trạng thái thành công');
    }
}
