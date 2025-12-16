@extends('layout.index')

@section('content')
<div class="container" style="max-width:700px">

    <h3 class="mb-3">💰 Nạp tiền vào tài khoản</h3>

    {{-- Thông tin chuyển khoản --}}
    <div class="alert alert-info">
        <strong>Thông tin chuyển khoản</strong><br>
        Ngân hàng: <b>Vietcombank</b><br>
        Số tài khoản: <b>0123456789</b><br>
        Chủ tài khoản: <b>CTY INDOCHINE</b><br>
        Nội dung: <b>NAP {{ auth()->user()->id }}</b>
    </div>

    {{-- Thông báo --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Form --}}
    <form method="POST" action="{{ route('wallet.deposit.submit') }}">
        @csrf

        <div class="mb-3">
            <label>Số tiền đã chuyển</label>
            <input type="number" name="amount"
                   class="form-control"
                   min="10000"
                   required>
        </div>

        <div class="mb-3">
            <label>Ngân hàng bạn chuyển</label>
            <input type="text" name="bank_name"
                   class="form-control"
                   required>
        </div>

        <div class="mb-3">
            <label>Mã giao dịch / nội dung chuyển khoản</label>
            <input type="text" name="transaction_code"
                   class="form-control"
                   required>
        </div>

        <button class="btn btn-primary w-100">
            Gửi yêu cầu nạp tiền
        </button>
    </form>

</div>
@endsection
