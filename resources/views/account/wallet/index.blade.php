@extends('layout.index')

@section('title') Công Ty Cổ Phần Bất Động Sản Indochine @endsection
@section('description') Công Ty Cổ Phần Bất Động Sản Indochine là công ty thành viên của Đất Xanh Miền Bắc - UY TÍN số 1 thị trường BĐS Việt Nam @endsection
@section('robots') index, follow @endsection
@section('url'){{asset('')}}@endsection

@section('css')
<link href="assets/css/widget.css" rel="stylesheet">
<link href="assets/css/news.css" rel="stylesheet">
<link href="assets/css/account.css" rel="stylesheet">
@endsection

@section('content')
@include('account.layout.menu')
<section class="card-grid news-sec">
    <div class="container">
        <div class="row">
            <div class="col-lg-2">
                @include('account.layout.sitebar')
            </div>

            <div class="col-lg-10">
                <h3 class="mb-3">💰 Ví của tôi</h3>

                <p><a href="{{ route('wallet.deposit.form') }}">Nạp tiền</a></p>

                {{-- Số dư --}}
                <div class="alert alert-success">
                    <strong>Số tiền của bạn:</strong>{{ number_format($wallet->balance) }} đ | 
                    <strong>Số tiền tạm giữ:</strong>{{ number_format($wallet->held_balance) }} đ
                </div>

                {{-- Bộ lọc --}}
                <!-- <form method="GET" class="row g-2 mb-3">
                    <div class="col-md-3">
                        <select name="type" class="form-control">
                            <option value="">-- Tất cả giao dịch --</option>
                            <option value="deposit" {{ request('type')=='deposit'?'selected':'' }}>
                                Nạp tiền
                            </option>
                            <option value="withdraw" {{ request('type')=='withdraw'?'selected':'' }}>
                                Trừ tiền
                            </option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <input type="" name="from_date" class="form-control"
                               value="{{ request('from_date') }}">
                    </div>

                    <div class="col-md-3">
                        <input type="" name="to_date" class="form-control"
                               value="{{ request('to_date') }}">
                    </div>

                    <div class="col-md-3">
                        <button class="btn btn-primary w-100">Lọc</button>
                    </div>
                </form> -->

                {{-- Bảng sao kê --}}
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Thời gian</th>
                            <th>Loại</th>
                            <th>Số tiền</th>
                            <th>Ghi chú</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                                @php
    // Map type => [label, badgeClass, sign, textClass]
    $typeMap = [
        'deposit'  => ['Nạp tiền',        'bg-success', '+', 'text-success'],
        'withdraw' => ['Trừ tiền',        'bg-danger',  '-', 'text-danger'],
        'rollback' => ['Hoàn/rollback',   'bg-warning', '+', 'text-warning'],

        // NEW
        'hold'     => ['Giữ tiền (Hold)', 'bg-info',    '-', 'text-info'],
        'release'  => ['Nhả giữ (Release)','bg-secondary','+','text-secondary'],
        'capture'  => ['Nghiệm thu (Trừ)', 'bg-primary','-', 'text-primary'],
        'refund'   => ['Hoàn tiền',        'bg-warning','+','text-warning'],
    ];

    $t = $typeMap[$item->type] ?? ['Khác', 'bg-dark', '', 'text-dark'];
@endphp

<td>
    <span class="badge {{ $t[1] }}">{{ $t[0] }}</span>
</td>

<td class="{{ $t[3] }}">
    {{ $t[2] }}
    {{ number_format($item->amount) }} đ
</td>

                               @php
    $amountUi = [
        'deposit'  => ['text-success', '+'],
        'withdraw' => ['text-danger',  '-'],
        'rollback' => ['text-warning', '+'],

        // NEW
        'hold'     => ['text-info',    '-'], // giữ tiền: giảm available
        'release'  => ['text-secondary','+'],// nhả giữ: tăng available
        'capture'  => ['text-primary', '-'], // nghiệm thu: trừ thật từ held
        'refund'   => ['text-warning', '+'], // hoàn tiền
    ];

    [$cls, $sign] = $amountUi[$item->type] ?? ['text-dark', ''];
@endphp

<td class="{{ $cls }}">
    {{ $sign }} {{ number_format($item->amount) }} đ
</td>

                                <td>{{ $item->description }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">
                                    Chưa có giao dịch
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- Phân trang --}}
                {{ $transactions->links() }}
            </div>
            
            

        </div>
    </div>
</section>
<!------------------- END CARD ------------------->

@endsection


@section('script')

@endsection
