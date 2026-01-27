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
use Illuminate\Support\Str;

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

    if ($request->filled('bank')) {
        $query->where('bank_name', $request->bank);
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

        if (preg_match('/^(\d{1,2}\/\d{1,2}\/\d{4})\s*-\s*(\d{1,2}\/\d{1,2}\/\d{4})$/', $range, $m)) {

            $tz = config('app.timezone'); // vd: Asia/Ho_Chi_Minh

            // input bạn đang nhận là m/d/Y (vì có kiểu 12/31/2025)
            $from = Carbon::createFromFormat('m/d/Y', $m[1], $tz)->startOfDay()->utc();
            $to   = Carbon::createFromFormat('m/d/Y', $m[2], $tz)->endOfDay()->utc();

            $query->whereBetween('created_at', [$from, $to]);
        }
    }



    $deposits = $query->paginate(100)->withQueryString();

    $sumAmount = $deposits->sum(function ($t) {
        return (float)($t->amount ?? 0);
    });

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

    return view('account.deposit.index', compact('deposits', 'departmentOptions', 'sumAmount'));
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

    public function updateBankName(Request $request, Deposit $deposit)
    {
        // nếu có phân quyền thì mở ra:
        // $this->authorize('update', $deposit);

        $request->validate([
            'bank_name' => ['required', 'string', 'max:255'],
        ]);

        $deposit->bank_name = $request->bank_name;
        $deposit->save();

        // Nếu bạn muốn lưu lịch sử giống hệ thống histories:
        // $deposit->histories()->create([
        //     'admin_id' => auth()->id(),
        //     'action' => 'bank_name',
        //     'note' => 'Update bank: '.$request->bank_name,
        // ]);

        return response()->json([
            'ok' => true,
            'bank_name' => $deposit->bank_name,
        ]);
    }



    public function depositForm()
    {
        $user = auth()->user();
        $wallet = $user->wallet()->firstOrCreate(['user_id' => $user->id]);

        $activeDeposit = Deposit::where('user_id', $user->id)
            ->where('status', 'pending_upload')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        $deposits = Deposit::where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        return view('account.wallet.deposit', compact('deposits','wallet','user','activeDeposit'));
    }


    public function depositCreate(Request $request)
    {
        $request->validate([
            'amount' => ['required','integer','min:1000', function($attr,$value,$fail){
                if ($value % 1000 !== 0) $fail('Số tiền phải là bội số của 1.000');
            }],
        ]);

        $user = auth()->user();

        // (Tuỳ bạn) chặn spam nếu đã có lệnh pending_upload chưa hết hạn
        $active = Deposit::where('user_id', $user->id)
            ->where('status', 'pending_upload')
            ->where('expires_at', '>', now())
            ->latest()->first();

        if ($active) {
            return redirect()
                ->route('wallet.deposit.form')
                ->withErrors(['amount' => 'Bạn đang có 1 lệnh nạp đang chờ upload UNC. Vui lòng hoàn tất hoặc chờ hết hạn.']);
        }

        $bankAccount = "0977572947";
        $beneficiary = "NGUYỄN VĂN TUẤN";
        $bankName    = "VPBANK";

        $deposit = Deposit::create([
            'user_id' => $user->id,
            'amount' => (int)$request->amount,
            'status' => 'pending_upload',
            'expires_at' => now()->addMinutes(10),
            // 'expires_at' => now()->addSeconds(10),
            'transaction_code' => 'NAP' . $user->id . now()->format('YmdHis') . Str::upper(Str::random(4)),
            'bank_account' => $bankAccount,
            'beneficiary' => $beneficiary,
            'bank_name' => $bankName,
        ]);

        // Bạn render QR theo code/amount (tuỳ ngân hàng/chuẩn QR bạn dùng)
        // Nếu bạn đang dùng ảnh QR tĩnh (tuan.jpg), vẫn OK — miễn nội dung CK dùng $deposit->code.

        return redirect()->route('wallet.deposit.form')->with('open_deposit_id', $deposit->id);
    }


    public function depositUploadProof(Request $request, Deposit $deposit)
    {
        abort_unless($deposit->user_id === auth()->id(), 403);

        // Không cho upload nếu lệnh đã hết hạn / bị hủy
        if (!$deposit->expires_at || now()->greaterThanOrEqualTo($deposit->expires_at)) {
            // mark expired nếu chưa mark
            if ($deposit->status === 'pending_upload') {
                $deposit->update(['status' => 'expired']);
            }
            return back()->withErrors(['proof_image' => 'Lệnh nạp đã hết hạn (10 phút). Vui lòng tạo lệnh mới.']);
        }

        if ($deposit->status !== 'pending_upload') {
            return back()->withErrors(['proof_image' => 'Lệnh nạp không còn ở trạng thái chờ upload.']);
        }

        $request->validate([
            'proof_image' => 'required|image|mimes:jpg,jpeg,png,webp|max:20480',
        ]);

        $file = $request->file('proof_image');
        $filename = $deposit->code . '_' . time() . '.' . $file->getClientOriginalExtension();

        $path = $file->storeAs('deposits', $filename, 'public');

        $deposit->update([
            'proof_image' => $path,
            'proof_uploaded_at' => now(),
            'status' => 'pending',
        ]);

        return redirect()
            ->route('wallet.deposit.form')
            ->with('success', 'Đã upload UNC. Vui lòng chờ admin duyệt.');
    }

    public function depositExpire(Request $request, Deposit $deposit)
    {
        abort_unless($deposit->user_id === auth()->id(), 403);

        // Force expire nếu đã quá hạn và vẫn pending_upload
        $updated = Deposit::where('id', $deposit->id)
            ->where('user_id', auth()->id())
            ->where('status', 'pending_upload')
            ->whereNotNull('expires_at')
            ->whereRaw('expires_at <= NOW()')
            ->update(['status' => 'expired']);

        return response()->json([
            'ok' => true,
            'updated' => $updated,               // 1 = đã update, 0 = không update
            'status_now' => Deposit::find($deposit->id)->status,
        ]);
    }















}
