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

    .login-tabs {
        display: flex;
        justify-content: center;
        margin-bottom: 20px;
        gap: 15px;
    }
    
    .login-tab {
        background: none;
        border: none;
        padding: 10px 20px;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.3s;
        outline: none;
    }

    .login-tab:hover {
        color: #007bff;
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

                    <div class="">
                        <a href="{{ route('google.redirect') }}" class="goole">
                            <button type="button" class="login btn btn-light btn-lg d-flex align-items-center shadow-sm border rounded-pill">
                                <img src="https://img.icons8.com/color/48/000000/google-logo.png" alt="Google Logo" class="me-2" width="24" height="24">
                                <span>Đăng nhập bằng GOOGLE</span>
                            </button>
                        </a>
                        <p class="google-login-note">
                            Dùng mail khác hãy bấm <a href="{{ route('google.redirect', ['select_account' => 1]) }}">vào đây</a>
                        </p>
                        
                    </div>

                    <div class="login-alert mt-3">
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
                if (item === tab) {
                    item.style.fontWeight = 'bold';
                    item.style.borderBottom = '2px solid #007bff';
                } else {
                    item.style.fontWeight = 'normal';
                    item.style.borderBottom = 'none';
                }
            });

            panels.forEach(function (panel) {
                if (panel.getAttribute('data-login-panel') === target) {
                    panel.style.display = 'block';
                    panel.classList.add('is-active');
                } else {
                    panel.style.display = 'none';
                    panel.classList.remove('is-active');
                }
            });
        });
    });

    // Initialize tabs styling
    document.querySelector('[data-login-tab="google"]').click();
});
</script>
@endsection
