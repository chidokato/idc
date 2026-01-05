<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        return view('account.deposit.index', compact('deposits'));
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

        DB::transaction(function () use ($request, $deposit) {

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
        });

        return back()->with('success', 'Cập nhật trạng thái thành công');
    }
}
