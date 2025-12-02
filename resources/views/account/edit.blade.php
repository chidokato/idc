@extends('layout.index')

@section('title') Công Ty Cổ Phần Bất Động Sản Indochine @endsection
@section('description') Công Ty Cổ Phần Bất Động Sản Indochine là công ty thành viên của Đất Xanh Miền Bắc - UY TÍN số 1 thị trường BĐS Việt Nam @endsection
@section('robots') index, follow @endsection
@section('url'){{asset('')}}@endsection

@section('css')
<link href="assets/css/widget.css" rel="stylesheet">
<link href="assets/css/news.css" rel="stylesheet">
<link href="assets/css/account.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('content')


<section class="card-grid news-sec">
    <div class="container">
        <div class="row">
            <div class="col-lg-2 d-none d-lg-block">
                @include('account.layout.sitebar')
            </div>

            <div class="col-lg-10">
                <form action="{{ route('account.update') }}" method="POST">
                    @csrf
                    <div class="row input-group">
                        <div class="col-md-2"></div>
                        <div class="col-md-5"><h3>Sửa thông tin tài khoản</h3></div>
                    </div>
                    <div class="row input-group">
                        <div class="col-md-2"><label>Họ tên <span class="required">(*)</span></label></div>
                        <div class="col-md-5"><input required type="text" name="yourname" class="form-control" value="{{ $user->yourname }}" placeholder="Họ & Tên"></div>
                    </div>
                    <div class="row input-group">
                        <div class="col-md-2"><label>Nhóm / Sàn <span class="required">(*)</span> </label></div>
                        <div class="col-md-5">
                            <select class="form-control select2" name="department_id" required>
                                <option value="">---</option>
                                {!! $departmentOptions !!}
                            </select>
                        </div>
                    </div>
                    <div class="row input-group">
                        <div class="col-md-2"><label>Email <span class="required">(*)</span></label></div>
                        <div class="col-md-5"><input readonly type="text" name="email" class="form-control" value="{{ $user->email }}" placeholder="Email"></div>
                    </div>
                    <div class="row input-group">
                        <div class="col-md-2"><label>Số điện thoại</label></div>
                        <div class="col-md-5"><input type="text" name="phone" class="form-control" value="{{ $user->phone }}" placeholder="Số điện thoại"></div>
                    </div>
                    <div class="row input-group">
                        <div class="col-md-2"><label>Địa chỉ</label></div>
                        <div class="col-md-5"><input type="text" class="form-control" name="address" value="{{ $user->address }}" placeholder="Địa chỉ"></div>
                    </div>
                    <div class="row input-group">
                        <div class="col-md-2"></div>
                        <div class="col-md-5">
                            <button type="submit">Lưu thay đổi</button>
                            <p class="required">Nhóm / Sàn chỉ thay đổi được 1 lần. Vui lòng kiểm tra kỹ trước khi cập nhật !!!</p>
                            <p class="mt-2"><i>Lỗi hoặc không thực hiện được xin liên hệ: 0977572947 (zalo)</i></p>
                        </div>
                    </div>
                    
                </form>

            </div>
            
        </div>
    </div>
</section>
<!------------------- END CARD ------------------->

@endsection


@section('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2();
    });
</script>
@endsection