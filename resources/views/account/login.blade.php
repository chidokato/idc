@extends('layout.index')

@section('title') Trang đăng nhập @endsection
@section('description') Công Ty Cổ Phần Bất Động Sản Indochine là công ty thành viên của Đất Xanh Miền Bắc - UY TÍN số 1 thị trường BĐS Việt Nam @endsection
@section('robots') index, follow @endsection
@section('url'){{ asset('') }}@endsection

@section('css')
<link href="assets/css/account.css" rel="stylesheet">
<style>
    .google-login-note {
        margin-top: 1rem;
        font-size: .95rem;
        line-height: 1.6;
        color: #4b5563;
        text-align: center;
    }

    .google-login-note a {
        color: #dc2626;
        font-weight: 700;
        text-decoration: underline;
    }

    .google-login-note a:hover {
        color: #b91c1c;
    }
</style>
@endsection

@section('content')

<section class="account">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="box-login">
                    <h1>ĐĂNG NHẬP VÀO HỆ THỐNG NỘI BỘ</h1>

                    <div class="login-tab-panel is-active" data-login-panel="google">
                        <a href="{{ route('google.redirect') }}" class="goole">
                            <button type="button" class="login btn btn-light btn-lg d-flex align-items-center shadow-sm border rounded-pill">
                                <img src="https://img.icons8.com/color/48/000000/google-logo.png" alt="Google Logo" class="me-2" width="24" height="24">
                                <span>Đăng nhập bằng GOOGLE</span>
                            </button>
                        </a>
                        <p class="google-login-note">
                            Dùng mail nội bộ của công ty (@dxmb.vn) để đăng nhập vào hệ thống.
                            Trường hợp muốn dùng mail khác hãy bấm <a href="{{ route('google.redirect', ['select_account' => 1]) }}">vào đây</a>
                            để chọn mail khác!
                        </p>
                    </div>

                    <div class="login-alert">
                        @include('admin.alert')
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tabs = document.querySelectorAll('[data-login-tab]');
    const panels = document.querySelectorAll('[data-login-panel]');

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            const target = tab.getAttribute('data-login-tab');

            tabs.forEach(function (item) {
                item.classList.toggle('is-active', item === tab);
            });

            panels.forEach(function (panel) {
                panel.classList.toggle('is-active', panel.getAttribute('data-login-panel') === target);
            });
        });
    });
});
</script>
@endsection
