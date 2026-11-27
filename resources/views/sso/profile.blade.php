<!DOCTYPE html>
<html lang="vi">
<head>
    <base href="{{ asset('') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chuẩn hóa thông tin đăng nhập - Indochine Real Estate</title>
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/common.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/header.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/responsive.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --idc-navy: #1a2c5e;
            --idc-navy-dark: #0f1a33;
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
            font-family: 'Segoe UI', Roboto, Arial, sans-serif;
            color: #1e293b;
            display: flex;
            flex-direction: column;
        }
        .main-wrap {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 28px 20px;
        }
        .profile-shell {
            width: 100%;
            max-width: 1080px;
            background: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 22px 55px rgba(26, 44, 94, 0.12), 0 4px 16px rgba(177, 147, 77, 0.08);
            border: 1px solid rgba(177, 147, 77, 0.30);
            display: grid;
            grid-template-columns: 330px 1fr;
        }
        .profile-sidebar {
            background: linear-gradient(165deg, var(--idc-navy) 0%, var(--idc-navy-dark) 100%);
            color: #ffffff;
            padding: 36px 28px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border-right: 4px solid var(--idc-gold);
        }
        .avatar-circle {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: rgba(177, 147, 77, 0.22);
            border: 2px solid var(--idc-gold-light);
            color: #ffffff;
            font-size: 26px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 18px;
            letter-spacing: 1px;
        }
        .sidebar-name {
            font-size: 21px;
            font-weight: 700;
            margin: 0 0 8px 0;
            color: #ffffff;
            line-height: 1.3;
        }
        .emp-code-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.10);
            border: 1px solid rgba(209, 181, 114, 0.55);
            color: var(--idc-gold-light);
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.4px;
        }
        .gold-divider {
            width: 48px;
            height: 3px;
            background: var(--idc-gold);
            border-radius: 2px;
            margin: 20px 0 16px;
        }
        .sidebar-note {
            font-size: 13px;
            line-height: 1.6;
            color: rgba(255, 255, 255, 0.78);
            margin: 0;
        }
        .sidebar-actions {
            margin-top: 28px;
            padding-top: 18px;
            border-top: 1px solid rgba(255, 255, 255, 0.14);
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .btn-side-apps {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 14px;
            border-radius: 9px;
            border: 1px solid rgba(255, 255, 255, 0.30);
            color: #ffffff;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: all .18s ease;
        }
        .btn-side-apps:hover {
            background: rgba(255, 255, 255, 0.12);
            color: #ffffff;
            text-decoration: none;
        }
        .btn-side-logout {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 14px;
            border-radius: 9px;
            background: rgba(220, 38, 38, 0.18);
            border: 1px solid rgba(248, 113, 113, 0.45);
            color: #fecaca;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: all .18s ease;
        }
        .btn-side-logout:hover {
            background: #dc2626;
            border-color: #dc2626;
            color: #ffffff;
            text-decoration: none;
        }
        .profile-main {
            padding: 38px 42px;
            background: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .main-title {
            font-size: 23px;
            font-weight: 700;
            color: var(--idc-navy);
            margin: 0 0 6px 0;
        }
        .main-subtitle {
            font-size: 13.5px;
            color: #64748b;
            margin: 0 0 26px 0;
            line-height: 1.5;
        }
        .fields-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px 22px;
        }
        .field-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--idc-navy);
            margin-bottom: 7px;
        }
        .input-box {
            display: flex;
            align-items: stretch;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            overflow: hidden;
            background: #ffffff;
            transition: border-color .18s ease, box-shadow .18s ease;
            height: 46px;
        }
        .input-box:focus-within {
            border-color: var(--idc-gold);
            box-shadow: 0 0 0 3px rgba(177, 147, 77, 0.18);
        }
        .input-box-icon {
            width: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8fafc;
            color: var(--idc-navy);
            border-right: 1px solid #e2e8f0;
            flex-shrink: 0;
            font-size: 14px;
        }
        .input-box-control {
            flex: 1;
            border: 0 !important;
            outline: none !important;
            box-shadow: none !important;
            padding: 0 14px;
            font-size: 14.5px;
            color: #0f172a;
            background: #ffffff;
            min-width: 0;
            height: 100%;
        }
        .input-box-eye {
            border: 0;
            border-left: 1px solid #e2e8f0;
            background: #f8fafc;
            color: #64748b;
            padding: 0 13px;
            cursor: pointer;
        }
        .input-box-eye:hover {
            color: var(--idc-navy);
        }
        .btn-idc-save {
            width: 100%;
            height: 48px;
            border: 0;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--idc-navy) 0%, #263f85 100%);
            color: #ffffff;
            font-size: 14.5px;
            font-weight: 700;
            letter-spacing: 0.4px;
            cursor: pointer;
            transition: all .2s ease;
            margin-top: 26px;
        }
        .btn-idc-save:hover {
            background: linear-gradient(135deg, var(--idc-gold) 0%, #9b7e3c 100%);
            box-shadow: 0 10px 24px rgba(177, 147, 77, 0.28);
        }
        .form-note-bar {
            margin-top: 14px;
            font-size: 12.5px;
            color: #64748b;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        @media (max-width: 920px) {
            .profile-shell {
                grid-template-columns: 1fr;
            }
            .profile-sidebar {
                border-right: 0;
                border-bottom: 3px solid var(--idc-gold);
                padding: 18px 18px;
            }
            .profile-main {
                padding: 22px 18px;
            }
            .fields-grid {
                grid-template-columns: 1fr;
                gap: 12px;
            }
        }
        @media (max-width: 575.98px) {
            .main-wrap {
                padding: 12px 10px;
                align-items: flex-start;
            }
            .profile-shell {
                border-radius: 14px;
            }
            .profile-sidebar {
                padding: 14px 16px;
            }
            .sidebar-identity {
                display: flex;
                align-items: center;
                gap: 12px;
            }
            .avatar-circle {
                width: 46px;
                height: 46px;
                font-size: 17px;
                margin-bottom: 0;
                flex-shrink: 0;
            }
            .sidebar-name {
                font-size: 16px;
                margin-bottom: 4px;
            }
            .emp-code-pill {
                padding: 3px 9px;
                font-size: 11.5px;
            }
            .gold-divider,
            .sidebar-note {
                display: none;
            }
            .sidebar-actions {
                margin-top: 12px;
                padding-top: 12px;
                flex-direction: row;
                gap: 8px;
            }
            .btn-side-apps,
            .btn-side-logout {
                flex: 1;
                padding: 8px 10px;
                font-size: 12px;
            }
            .profile-main {
                padding: 18px 16px;
            }
            .main-title {
                font-size: 18px;
                margin-bottom: 4px;
            }
            .main-subtitle {
                font-size: 12.5px;
                margin-bottom: 14px;
            }
            .field-label {
                font-size: 12px;
                margin-bottom: 5px;
            }
            .input-box {
                height: 42px;
            }
            .input-box-icon {
                width: 38px;
                font-size: 13px;
            }
            .input-box-control {
                font-size: 13.5px;
                padding: 0 10px;
            }
            .btn-idc-save {
                height: 44px;
                font-size: 13.5px;
                margin-top: 16px;
            }
            .form-note-bar {
                font-size: 11.5px;
                margin-top: 10px;
            }
        }
    </style>
</head>
<body>
@include('layout.header')

@php
    $nameParts = preg_split('/\s+/', trim((string) $profile->name));
    $initials = '';
    if (count($nameParts) >= 2) {
        $initials = mb_strtoupper(mb_substr($nameParts[0], 0, 1) . mb_substr(end($nameParts), 0, 1));
    } else {
        $initials = mb_strtoupper(mb_substr((string) $profile->name, 0, 2));
    }
@endphp

<div class="main-wrap">
    <div class="profile-shell">
        {{-- CỘT TRÁI: ĐỊNH DANH NHÂN SỰ --}}
        <div class="profile-sidebar">
            <div class="sidebar-identity">
                <div class="avatar-circle">{{ $initials }}</div>
                <div>
                    <h2 class="sidebar-name">{{ $profile->name }}</h2>
                    <div class="emp-code-pill">
                        <i class="fa-solid fa-lock"></i>
                        <span>Mã NV: {{ $profile->employee_code }}</span>
                    </div>
                </div>
            </div>
            <div>
                <div class="gold-divider"></div>
                <p class="sidebar-note">
                    Hồ sơ được liên kết từ dữ liệu gốc <strong>HCC</strong>. Mã nhân viên là định danh cố định và không thể thay đổi.
                </p>
            </div>

            <div class="sidebar-actions">
                @if($profile->standardized_at)
                    <a href="{{ route('sso.apps') }}" class="btn-side-apps">
                        <i class="fa-solid fa-grip"></i> Chọn phần mềm
                    </a>
                @endif
                <a href="{{ route('sso.logout') }}" class="btn-side-logout">
                    <i class="fa-solid fa-power-off"></i> Đăng xuất
                </a>
            </div>
        </div>

        {{-- CỘT PHẢI: FORM CHUẨN HÓA --}}
        <div class="profile-main">
            <h1 class="main-title">Chuẩn hóa thông tin đăng nhập</h1>
            <p class="main-subtitle">
                Cập nhật Email, Số điện thoại và Mật khẩu dùng chung để đồng bộ trên toàn bộ hệ thống Indochine Real Estate.
            </p>

            <form action="{{ route('sso.profile.update') }}" method="POST">
                @csrf

                <div class="fields-grid">
                    <div>
                        <label for="primary_email" class="field-label">
                            Email đăng nhập chính <span class="text-danger">*</span>
                        </label>
                        <div class="input-box">
                            <span class="input-box-icon"><i class="fa-solid fa-envelope"></i></span>
                            <input type="email"
                                   id="primary_email"
                                   name="primary_email"
                                   class="input-box-control"
                                   value="{{ old('primary_email', $profile->primary_email) }}"
                                   placeholder="email@indochine.vn"
                                   required>
                        </div>
                    </div>

                    <div>
                        <label for="primary_phone" class="field-label">
                            Số điện thoại chính <span class="text-danger">*</span>
                        </label>
                        <div class="input-box">
                            <span class="input-box-icon"><i class="fa-solid fa-phone"></i></span>
                            <input type="text"
                                   id="primary_phone"
                                   name="primary_phone"
                                   class="input-box-control"
                                   value="{{ old('primary_phone', $profile->primary_phone) }}"
                                   placeholder="09xxxxxxxx"
                                   required>
                        </div>
                    </div>

                    <div>
                        <label for="new_password" class="field-label">
                            Mật khẩu mới đồng bộ <span class="text-danger">*</span>
                        </label>
                        <div class="input-box">
                            <span class="input-box-icon"><i class="fa-solid fa-lock"></i></span>
                            <input type="password"
                                   id="new_password"
                                   name="new_password"
                                   class="input-box-control"
                                   placeholder="Tối thiểu 6 ký tự"
                                   autocomplete="new-password"
                                   required>
                            <button type="button" class="input-box-eye" onclick="toggleField('new_password', 'eye1')" tabindex="-1">
                                <i class="fa-regular fa-eye" id="eye1"></i>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label for="new_password_confirmation" class="field-label">
                            Xác nhận mật khẩu mới <span class="text-danger">*</span>
                        </label>
                        <div class="input-box">
                            <span class="input-box-icon"><i class="fa-solid fa-shield-halved"></i></span>
                            <input type="password"
                                   id="new_password_confirmation"
                                   name="new_password_confirmation"
                                   class="input-box-control"
                                   placeholder="Nhập lại mật khẩu mới"
                                   autocomplete="new-password"
                                   required>
                            <button type="button" class="input-box-eye" onclick="toggleField('new_password_confirmation', 'eye2')" tabindex="-1">
                                <i class="fa-regular fa-eye" id="eye2"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="form-note-bar">
                    <i class="fa-solid fa-circle-info" style="color: var(--idc-gold);"></i>
                    <span>Mật khẩu mới sẽ được áp dụng đồng thời cho HCC, MKT, CRM, Learning, IDC Tool &amp; Web chính.</span>
                </div>

                <button type="submit" class="btn-idc-save">
                    <i class="fa-solid fa-check-circle me-1"></i>
                    @if(!empty($pendingApp))
                        LƯU CHUẨN HÓA &amp; QUAY VỀ {{ mb_strtoupper($pendingApp['short']) }}
                    @else
                        LƯU CHUẨN HÓA &amp; CHỌN PHẦN MỀM
                    @endif
                </button>
            </form>
        </div>
    </div>
</div>

<script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function toggleField(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
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
            title: 'Chuẩn hóa thông tin',
            text: @json(session('info')),
            confirmButtonColor: '#1a2c5e',
            confirmButtonText: 'Đã hiểu'
        });
    @elseif($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Dữ liệu chưa hợp lệ',
            html: `{!! implode('<br>', array_map('e', $errors->all())) !!}`,
            confirmButtonColor: '#1a2c5e',
            confirmButtonText: 'Kiểm tra lại'
        });
    @endif
});
</script>
</body>
</html>
