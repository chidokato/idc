<!DOCTYPE html>
<html lang="vi">
<head>
    <base href="{{ asset('') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập tập trung - Indochine Real Estate</title>
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/common.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/header.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/responsive.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --idc-navy: #1a2c5e;
            --idc-navy-dark: #101c3d;
            --idc-gold: #b1934d;
            --idc-gold-light: #d1b572;
        }
        *, *::before, *::after {
            box-sizing: border-box;
        }
        body {
            margin: 0;
            min-height: 100vh;
            background: #ffffff;
            display: flex;
            flex-direction: column;
            font-family: 'Segoe UI', Roboto, Arial, sans-serif;
            color: #1e293b;
        }
        .sso-page-main {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 36px 20px;
        }
        .sso-shell {
            width: 100%;
            max-width: 960px;
            background: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 24px 60px rgba(26, 44, 94, 0.12), 0 4px 16px rgba(177, 147, 77, 0.10);
            border: 1px solid rgba(177, 147, 77, 0.30);
            display: grid;
            grid-template-columns: 400px 1fr;
        }
        .sso-brand {
            background: linear-gradient(165deg, var(--idc-navy) 0%, var(--idc-navy-dark) 100%);
            color: #ffffff;
            padding: 44px 36px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border-right: 4px solid var(--idc-gold);
        }
        .brand-logo-box {
            background: #ffffff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 18px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.22);
            margin-bottom: 28px;
            width: fit-content;
        }
        .brand-logo-box img {
            height: 60px;
            width: auto;
            display: block;
        }
        .brand-kicker {
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 1.6px;
            text-transform: uppercase;
            color: var(--idc-gold-light);
            margin-bottom: 8px;
        }
        .brand-title {
            font-size: 26px;
            font-weight: 700;
            line-height: 1.3;
            margin: 0;
            color: #ffffff;
        }
        .gold-divider {
            width: 52px;
            height: 3px;
            background: var(--idc-gold);
            border-radius: 2px;
            margin: 16px 0 18px;
        }
        .brand-desc {
            font-size: 14px;
            line-height: 1.65;
            color: rgba(255, 255, 255, 0.84);
            margin: 0;
        }
        .app-target-badge {
            background: rgba(177, 147, 77, 0.16);
            border: 1px solid rgba(209, 181, 114, 0.55);
            border-radius: 10px;
            padding: 12px 14px;
            margin-top: 22px;
        }
        .brand-footer-note {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.72);
            border-top: 1px solid rgba(255, 255, 255, 0.14);
            padding-top: 16px;
            margin-top: 28px;
        }
        .sso-form-wrap {
            padding: 44px 44px;
            background: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .form-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 10px;
        }
        .form-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--idc-navy);
            margin: 0;
        }
        .home-link {
            font-size: 13px;
            color: #64748b;
            text-decoration: none;
            white-space: nowrap;
            font-weight: 500;
        }
        .home-link:hover {
            color: var(--idc-navy);
            text-decoration: none;
        }
        .form-subtitle {
            font-size: 14px;
            color: #64748b;
            line-height: 1.55;
            margin-bottom: 24px;
        }
        .field-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--idc-navy);
            margin-bottom: 8px;
        }
        .input-wrap {
            display: flex;
            align-items: stretch;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            overflow: hidden;
            background: #ffffff;
            transition: border-color .18s ease, box-shadow .18s ease;
        }
        .input-wrap:focus-within {
            border-color: var(--idc-gold);
            box-shadow: 0 0 0 3px rgba(177, 147, 77, 0.18);
        }
        .input-icon {
            width: 46px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #faf7f0;
            color: var(--idc-navy);
            border-right: 1px solid #e2e8f0;
            flex-shrink: 0;
        }
        .input-field {
            flex: 1;
            border: 0;
            outline: none;
            padding: 12px 14px;
            font-size: 15px;
            color: #0f172a;
            background: #ffffff;
            min-width: 0;
        }
        .eye-btn {
            border: 0;
            border-left: 1px solid #e2e8f0;
            background: #faf7f0;
            color: #64748b;
            padding: 0 14px;
            cursor: pointer;
        }
        .eye-btn:hover {
            color: var(--idc-navy);
        }
        .btn-idc-primary {
            width: 100%;
            height: 48px;
            border: 0;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--idc-navy) 0%, #263f85 100%);
            color: #ffffff;
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 0.4px;
            cursor: pointer;
            transition: all .2s ease;
            margin-top: 8px;
        }
        .btn-idc-primary:hover {
            background: linear-gradient(135deg, var(--idc-gold) 0%, #9b7e3c 100%);
            box-shadow: 0 8px 20px rgba(177, 147, 77, 0.28);
        }
        .sso-divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 18px 0;
            color: #94a3b8;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.6px;
        }
        .sso-divider::before,
        .sso-divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #e2e8f0;
        }
        .sso-divider::before { margin-right: 12px; }
        .sso-divider::after { margin-left: 12px; }
        .btn-google-sso {
            width: 100%;
            height: 46px;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            background: #ffffff;
            color: #1e293b;
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-decoration: none;
            transition: all .18s ease;
        }
        .btn-google-sso:hover {
            background: #faf7f0;
            border-color: var(--idc-gold);
            color: var(--idc-navy);
            text-decoration: none;
        }
        .form-footer {
            margin-top: 28px;
            padding-top: 16px;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12px;
            color: #64748b;
        }
        @media (max-width: 860px) {
            .sso-shell {
                grid-template-columns: 1fr;
            }
            .sso-brand {
                border-right: 0;
                border-bottom: 3px solid var(--idc-gold);
                padding: 22px 20px;
            }
            .sso-form-wrap {
                padding: 24px 20px;
            }
        }
        @media (max-width: 575.98px) {
            .sso-page-main {
                padding: 14px 10px;
                align-items: flex-start;
            }
            .sso-shell {
                border-radius: 14px;
            }
            .sso-brand {
                padding: 16px 18px;
            }
            .brand-logo-box {
                padding: 8px 12px;
                margin-bottom: 12px;
            }
            .brand-logo-box img {
                height: 40px;
            }
            .brand-title {
                font-size: 20px;
            }
            .gold-divider,
            .brand-desc,
            .brand-footer-note {
                display: none;
            }
            .sso-form-wrap {
                padding: 20px 18px;
            }
            .form-title {
                font-size: 20px;
            }
            .form-subtitle {
                font-size: 13px;
                margin-bottom: 16px;
            }
        }
    </style>
</head>
<body>
@include('layout.header')
<div class="sso-page-main">
<div class="sso-shell">
    <div class="sso-brand">
        <div>
            <div class="brand-logo-box">
                <img src="{{ asset('data/images/logo_-01.png') }}" alt="Indochine Real Estate">
            </div>

            <div class="brand-kicker">Indochine Ecosystem</div>
            <h1 class="brand-title">Cổng Đăng Nhập Tập Trung</h1>
            <div class="gold-divider"></div>
            <p class="brand-desc">
                Một tài khoản duy nhất để truy cập toàn bộ hệ thống phần mềm nội bộ: HCC, Marketing, CRM BĐS, Đào tạo &amp; Giỏ hàng dự án.
            </p>

            @if(!empty($targetApp))
                <div class="app-target-badge">
                    <div style="font-size: 11px; text-transform: uppercase; letter-spacing: .8px; color: var(--idc-gold-light);">
                        Đang chuyển tiếp từ phần mềm
                    </div>
                    <div style="font-weight: 700; margin-top: 4px; color: #fff;">
                        <i class="{{ $targetApp['icon'] }} me-1" style="color: var(--idc-gold-light);"></i>
                        {{ $targetApp['name'] }}
                    </div>
                </div>
            @endif
        </div>

        <div class="brand-footer-note">
            <i class="fa-solid fa-shield-halved me-1" style="color: var(--idc-gold-light);"></i>
            Xác thực chuẩn hóa theo Mã nhân viên (HCC)
        </div>
    </div>

    <div class="sso-form-wrap">
        <div class="form-header">
            <h2 class="form-title">Đăng nhập hệ thống</h2>
            <a href="{{ url('/') }}" class="home-link">
                <i class="fa-solid fa-house me-1"></i> Trang chủ
            </a>
        </div>
        <p class="form-subtitle">
            Nhập <strong>Email</strong>, <strong>Số điện thoại</strong>, <strong>Username</strong> hoặc <strong>Mã nhân viên</strong> đang dùng trên bất kỳ phần mềm nào của Indochine.
        </p>

        <form action="{{ route('sso.login.submit') }}" method="POST">
            @csrf
            @if(!empty($appKey))
                <input type="hidden" name="app" value="{{ $appKey }}">
            @endif

            <div class="mb-3">
                <label for="identifier" class="field-label">Tài khoản đăng nhập</label>
                <div class="input-wrap">
                    <span class="input-icon"><i class="fa-solid fa-user"></i></span>
                    <input type="text"
                           id="identifier"
                           name="identifier"
                           value="{{ old('identifier') }}"
                           class="input-field"
                           placeholder="Email / Số điện thoại / Mã NV..."
                           required
                           autofocus>
                </div>
            </div>

            <div class="mb-4">
                <label for="password" class="field-label">Mật khẩu</label>
                <div class="input-wrap">
                    <span class="input-icon"><i class="fa-solid fa-lock"></i></span>
                    <input type="password"
                           id="password"
                           name="password"
                           class="input-field"
                           placeholder="Nhập mật khẩu của bạn"
                           required>
                    <button type="button" class="eye-btn" onclick="togglePassword()" tabindex="-1" title="Hiện/ẩn mật khẩu">
                        <i class="fa-regular fa-eye" id="toggleEyeIcon"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-idc-primary">
                <i class="fa-solid fa-right-to-bracket me-1"></i> ĐĂNG NHẬP HỆ THỐNG
            </button>
        </form>

        <div class="sso-divider">hoặc</div>

        <a href="{{ route('sso.google', !empty($appKey) ? ['app' => $appKey] : []) }}" class="btn-google-sso">
            <svg width="18" height="18" viewBox="0 0 48 48" aria-hidden="true">
                <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
            </svg>
            Đăng nhập bằng Google
        </a>

        <div class="form-footer">
            <span>&copy; {{ date('Y') }} Indochine Real Estate</span>
            <span style="color: var(--idc-gold); font-weight: 700;">Single Sign-On</span>
        </div>
    </div>
</div>
</div>

<script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function togglePassword() {
    const input = document.getElementById('password');
    const icon = document.getElementById('toggleEyeIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

document.addEventListener('DOMContentLoaded', function () {
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Thành công',
            text: @json(session('success')),
            confirmButtonColor: '#1a2c5e',
            confirmButtonText: 'Đóng'
        });
    @elseif(session('info'))
        Swal.fire({
            icon: 'info',
            title: 'Thông báo',
            text: @json(session('info')),
            confirmButtonColor: '#1a2c5e',
            confirmButtonText: 'Đã hiểu'
        });
    @elseif($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Đăng nhập không thành công',
            html: `{!! implode('<br>', array_map('e', $errors->all())) !!}`,
            confirmButtonColor: '#1a2c5e',
            confirmButtonText: 'Thử lại'
        });
    @endif
});
</script>
</body>
</html>
