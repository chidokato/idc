<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use App\Models\WalletTransaction;
use App\Models\Deposit;

class WalletController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $wallet = $user->wallet()->firstOrCreate([
            'user_id' => $user->id
        ]);

        $query = WalletTransaction::where('wallet_id', $wallet->id);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $transactions = $query
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('account.wallet.index', compact('wallet', 'transactions'));
    }

    public function depositForm()
    {
        $deposits = Deposit::where('user_id', auth()->id())
        ->latest()
        ->paginate(10);
        return view('account.wallet.deposit', compact('deposits'));
    }


    public function depositSubmit(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10000',
            'bank_name' => 'required|string|max:255',
            'transaction_code' => 'required|string|max:255',
            'proof_image'      => 'required|image|max:2048', // tối đa 2MB
        ]);

        $user = Auth::user();

        $imagePath = null;
        if ($request->hasFile('proof_image')) {
            $file = $request->file('proof_image');

            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();

            $imagePath = $file->storeAs(
                'deposits',
                $filename,
                'public'
            );
        }

        Deposit::create([
            'user_id' => auth()->id(),
            'amount' => $request->amount,
            'bank_name' => $request->bank_name,
            'transaction_code' => $request->transaction_code,
            'proof_image'      => $imagePath,
            'status' => 'pending',
        ]);

        return redirect()
            ->route('wallet.deposit.form')
            ->with('success', 'Đã gửi yêu cầu nạp tiền. Vui lòng chờ admin duyệt.');
    }
}
