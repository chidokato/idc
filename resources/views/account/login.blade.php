@extends('layout.index')

@section('title') Trang đăng nhập @endsection
@section('description') Công Ty Cổ Phần Bất Động Sản Indochine là công ty thành viên của Đất Xanh Miền Bắc - UY TÍN số 1 thị trường BĐS Việt Nam @endsection
@section('robots') index, follow @endsection
@section('url'){{asset('')}}@endsection

@section('css')
<link href="assets/css/account.css" rel="stylesheet">
@endsection

@section('content')

<section class="account">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <a href="{{ route('google.redirect') }}">
                    <button class="login btn btn-light btn-lg d-flex align-items-center shadow-sm border rounded-pill ">
                      <img src="https://img.icons8.com/color/48/000000/google-logo.png" alt="Google Logo" class="me-2" width="24" height="24">
                      <span class="">Đăng nhập hệ thống qua GOOGLE</span>
                    </button>
                    <p style="text-align: center;">@include('admin.alert')</p>
                </a>
            </div>
        </div>
    </div>
</section>

@endsection


@section('script')

@endsection