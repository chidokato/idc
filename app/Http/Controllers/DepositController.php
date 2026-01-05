<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\Wallet;
use Illuminate\Http\Request;
use App\Models\Department;
use App\Helpers\TreeHelper;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DepositController extends Controller
{
    /**
     * Danh sách nạp tiền
     */

public function index(Request $request)
{
    // dd($request->only('yourname','from','to','status','department_id'));

    $query = Deposit::with(['user.department', 'histories.admin'])->latest();

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    if ($request->filled('yourname')) {
        $kw = trim($request->yourname);
        $query->whereHas('user', function ($q) use ($kw) {
            $q->where('yourname', 'like', "%{$kw}%");
        });
    }

    if ($request->filled('department_id')) {
        $rootId = (int) $request->department_id;
        $ids = Department::getChildIds($rootId);

        $query->whereHas('user', function ($q) use ($ids) {
            $q->whereIn('department_id', $ids);
        });
    }

    // ✅ Lọc theo khoảng thời gian từ input range (DD/MM/YYYY - DD/MM/YYYY)
    if ($request->filled('range')) {
        $range = trim($request->range);

        // ví dụ: "05/01/2026 - 12/01/2026"
        if (preg_match('/^(\d{2}\/\d{2}\/\d{4})\s*-\s*(\d{2}\/\d{2}\/\d{4})$/', $range, $m)) {
            try {
                $from = Carbon::createFromFormat('d/m/Y', $m[1])->startOfDay();
                $to   = Carbon::createFromFormat('d/m/Y', $m[2])->endOfDay();
                $query->whereBetween('created_at', [$from, $to]);
            } catch (\Exception $e) {
                // sai format thì bỏ qua filter (hoặc bạn có thể return lỗi)
            }
        }
    }

    $deposits = $query->paginate(30)->withQueryString();

    // Build options
    $departments = Department::orderBy('name')->get(['id', 'name', 'parent']);
    $departmentOptions = TreeHelper::buildOptions(
        $departments,
        0,
        '',
        $request->department_id,
        'id',
        'parent',
        'name'
    );

    return view('account.deposit.index', compact('deposits', 'departmentOptions'));
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
