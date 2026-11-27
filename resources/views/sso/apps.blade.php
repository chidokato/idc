<!DOCTYPE html>
<html lang="vi">
<head>
    <base href="{{ asset('') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chọn phần mềm làm việc - Indochine Real Estate</title>
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
            --idc-cream: #faf7f0;
        }
        body {
            min-height: 100vh;
            background: #ffffff;
            font-family: 'Segoe UI', Roboto, Arial, sans-serif;
            color: #1e293b;
        }
        .idc-topbar {
            background: linear-gradient(90deg, var(--idc-navy-dark), var(--idc-navy));
            border-bottom: 3px solid var(--idc-gold);
            color: #fff;
            padding: 12px 0;
        }
        .topbar-logo {
            background: #fff;
            border-radius: 8px;
            padding: 4px 10px;
            margin-right: 14px;
            display: inline-flex;
            align-items: center;
        }
        .topbar-logo img {
            height: 38px;
            width: auto;
        }
        .emp-badge {
            background: rgba(177, 147, 77, 0.22);
            border: 1px solid rgba(177, 147, 77, 0.55);
            color: var(--idc-gold-light);
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
        }
        .hero-banner {
            background: #ffffff;
            border-radius: 16px;
            padding: 26px 30px;
            color: var(--idc-navy);
            margin-bottom: 28px;
            border: 1px solid rgba(177, 147, 77, 0.30);
            border-left: 5px solid var(--idc-gold);
            box-shadow: 0 10px 26px rgba(26, 44, 94, 0.06);
        }
        .app-card {
            border: 1px solid rgba(177, 147, 77, 0.22);
            border-radius: 16px;
            box-shadow: 0 8px 22px rgba(26, 44, 94, 0.05);
            transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
            height: 100%;
            background: #fff;
        }
        .app-card:hover {
            transform: translateY(-4px);
            border-color: var(--idc-gold);
            box-shadow: 0 16px 34px rgba(26, 44, 94, 0.12);
        }
        .app-icon {
            width: 54px;
            height: 54px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 22px;
            flex-shrink: 0;
        }
        .btn-launch {
            background: var(--idc-cream);
            border: 1px solid rgba(177, 147, 77, 0.45);
            color: var(--idc-navy);
            font-weight: 600;
            border-radius: 10px;
            padding: 10px 14px;
            transition: all .18s ease;
            text-decoration: none;
            display: block;
            text-align: center;
        }
        .app-card:hover .btn-launch {
            background: var(--idc-navy);
            border-color: var(--idc-navy);
            color: #fff;
        }
        @media (max-width: 575.98px) {
            .container.py-4 {
                padding-top: 12px !important;
                padding-bottom: 18px !important;
            }
            .hero-banner {
                padding: 14px 16px;
                margin-bottom: 14px;
                border-radius: 12px;
            }
            .hero-banner h3 {
                font-size: 17px;
                margin-bottom: 2px !important;
            }
            .hero-banner p {
                font-size: 12px;
            }
            .apps-grid-row {
                --bs-gutter-x: 10px;
                --bs-gutter-y: 10px;
            }
            .app-card {
                border-radius: 12px;
            }
            .app-card .card-body {
                padding: 12px !important;
            }
            .app-card-head {
                flex-direction: column;
                align-items: flex-start !important;
                margin-bottom: 10px !important;
            }
            .app-icon {
                width: 38px;
                height: 38px;
                font-size: 16px;
                border-radius: 9px;
                margin-right: 0 !important;
                margin-bottom: 8px;
            }
            .app-card h5 {
                font-size: 14px;
                line-height: 1.25;
            }
            .app-desc {
                display: none;
            }
            .btn-launch {
                padding: 7px 10px;
                font-size: 12.5px;
                border-radius: 8px;
            }
        }
    </style>
</head>
<body>
@include('layout.header')

<div class="container py-4 py-md-5">
    <div class="hero-banner d-flex flex-wrap justify-content-between align-items-center">
        <div>
            <div class="small text-uppercase fw-bold" style="color: var(--idc-gold); letter-spacing: 1.2px;">
                Hệ sinh thái Indochine Real Estate &bull; {{ $profile->name }} ({{ $profile->employee_code }})
            </div>
            <h3 class="fw-bold mt-1 mb-1">Chọn phần mềm làm việc</h3>
            <p class="mb-0 small text-muted">
                Tài khoản đang hoạt động: <strong>{{ $profile->primary_email }}</strong> &bull; SĐT: <strong>{{ $profile->primary_phone }}</strong>
            </p>
        </div>
        <div class="mt-3 mt-md-0 d-flex gap-2">
            <a href="{{ route('sso.profile') }}" class="btn btn-sm btn-outline-primary">
                <i class="fa-solid fa-user-pen me-1"></i> Chuẩn hóa thông tin
            </a>
            <a href="{{ route('sso.logout') }}" class="btn btn-sm btn-danger">
                <i class="fa-solid fa-power-off me-1"></i> Đăng xuất
            </a>
        </div>
    </div>

    <div class="row g-4 apps-grid-row">
        @foreach($apps as $app)
            <div class="col-6 col-md-6 col-lg-4">
                <div class="card app-card">
                    <div class="card-body d-flex flex-column p-4">
                        <div class="d-flex align-items-center mb-3 app-card-head">
                            <div class="app-icon me-3" style="background: {{ $app['color'] }}">
                                <i class="{{ $app['icon'] }}"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-1" style="color: var(--idc-navy);">{{ $app['name'] }}</h5>
                                <span class="badge" style="background: rgba(177,147,77,0.16); color: #7c6228;">{{ $app['short'] }}</span>
                            </div>
                        </div>
                        <p class="text-muted small flex-grow-1 mb-4 app-desc">{{ $app['description'] }}</p>
                        <a href="{{ route('sso.launch', ['app' => $app['key']]) }}" class="btn-launch mt-auto">
                            Truy cập <i class="fa-solid fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
<script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Thành công',
            text: @json(session('success')),
            confirmButtonColor: '#1a2c5e',
            confirmButtonText: 'Đóng'
        });
    @endif
});
</script>
</body>
</html>
