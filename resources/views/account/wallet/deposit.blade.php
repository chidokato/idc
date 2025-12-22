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
                <h3 class="mb-3">💰 Nạp tiền vào tài khoản</h3>
                <div class="row">
                    <div class="col-lg-8">
                        {{-- Thông báo --}}
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        {{-- Form --}}
                        <form method="POST"
                              action="{{ route('wallet.deposit.submit') }}"
                              enctype="multipart/form-data">
                            @csrf

                            <div class="mb-3">
                                <label>Số tiền đã chuyển</label>
                                <input type="number"
                                       name="amount"
                                       class="form-control"
                                       min="10000"
                                       required>
                            </div>

                            <div class="mb-3">
                                <label>Ngân hàng bạn chuyển</label>
                                <input type="text"
                                       name="bank_name"
                                       class="form-control"
                                       required>
                            </div>

                            <div class="mb-3">
                                <label>Mã giao dịch / Nội dung chuyển khoản</label>
                                <input type="text"
                                       name="transaction_code"
                                       class="form-control"
                                       required>
                            </div>

                            <div class="mb-3">
                                <label>Ảnh chứng minh chuyển khoản</label>
                                <input type="file"
                                       name="proof_image"
                                       class="form-control"
                                       accept="image/*"
                                       required>
                            </div>

                            <button class="btn btn-primary w-100">
                                Gửi yêu cầu nạp tiền
                            </button>
                        </form>

                        {{-- Lịch sử nạp tiền --}}
                        <h3>Lịch sử nạp tiền</h3>

                        <table class="table table-bordered">
                        <tr>
                            <th>Ngày</th>
                            <th>Số tiền</th>
                            <th>Ngân hàng</th>
                            <th>Mã GD</th>
                            <th>Trạng thái</th>
                        </tr>

                        @foreach($deposits as $d)
                        <tr>
                            <td>{{ $d->created_at }}</td>
                            <td>{{ number_format($d->amount) }} đ</td>
                            <td>{{ $d->bank_name }}</td>
                            <td>{{ $d->transaction_code }}</td>
                            <td>
                                @if($d->status=='pending')
                                    ⏳ Chờ duyệt
                                @elseif($d->status=='approved')
                                    ✅ Đã duyệt
                                @else
                                    ❌ Từ chối
                                @endif
                            </td>
                        </tr>
                        @endforeach
                        </table>

                        {{ $deposits->links() }}
                    </div>
                    <div class="col-lg-4">
                        <div class="alert alert-info">
                            <strong>Thông tin chuyển khoản</strong><br>
                            Ngân hàng: <b>Vietcombank</b><br>
                            Số tài khoản: <b>0123456789</b><br>
                            Chủ tài khoản: <b>CTY INDOCHINE</b><br>
                            Nội dung: <b>NAP {{ auth()->user()->id }}</b>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!------------------- END CARD ------------------->

@endsection


@section('script')

@endsection

