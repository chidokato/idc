<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Centralized SSO Configuration
    |--------------------------------------------------------------------------
    */
    'secret' => env('SSO_SECRET', 'idc_sso_shared_secret_2026_change_in_prod'),
    'token_ttl' => (int) env('SSO_TOKEN_TTL', 300),
    'portal_url' => rtrim(env('SSO_PORTAL_URL', env('APP_URL', 'https://indochinerealestate.vn')), '/'),

    /*
    |--------------------------------------------------------------------------
    | Registered Functional Websites / Subdomains
    |--------------------------------------------------------------------------
    */
    'apps' => [
        'main' => [
            'key' => 'main',
            'name' => 'Indochine Portal & CMS',
            'short' => 'CMS Chính',
            'description' => 'Website chính & Hệ thống quản trị nội dung Indochine Real Estate.',
            'url' => rtrim(env('SSO_APP_MAIN_URL', 'https://indochinerealestate.vn'), '/'),
            'callback_path' => '/sso/local-login',
            'icon' => 'fa-solid fa-building',
            'color' => '#d8363a',
        ],
        'izi' => [
            'key' => 'izi',
            'name' => 'HCC / Nhân sự',
            'short' => 'HCC',
            'description' => 'Hệ thống quản lý nhân sự, chấm công, đánh giá & dữ liệu gốc nhân viên.',
            'url' => rtrim(env('SSO_APP_IZI_URL', 'https://hcc.indochinerealestate.vn'), '/'),
            'callback_path' => '/sso/callback',
            'icon' => 'fa-solid fa-users-gear',
            'color' => '#2563eb',
        ],
        'mkt' => [
            'key' => 'mkt',
            'name' => 'Indochine Marketing',
            'short' => 'MKT',
            'description' => 'Quản lý chiến dịch Marketing, task, chi phí và báo cáo hiệu suất.',
            'url' => rtrim(env('SSO_APP_MKT_URL', 'https://mkt.indochinerealestate.vn'), '/'),
            'callback_path' => '/sso/callback',
            'icon' => 'fa-solid fa-bullhorn',
            'color' => '#ea580c',
        ],
        'crm' => [
            'key' => 'crm',
            'name' => 'Indochine CRM BĐS',
            'short' => 'CRM BĐS',
            'description' => 'Quản lý khách hàng tiềm năng (Leads), chia số và chăm sóc khách hàng.',
            'url' => rtrim(env('SSO_APP_CRM_URL', 'https://crm.indochinerealestate.vn'), '/'),
            'callback_path' => '/sso/callback',
            'icon' => 'fa-solid fa-handshake',
            'color' => '#059669',
        ],
        'learning' => [
            'key' => 'learning',
            'name' => 'Indochine Learning',
            'short' => 'Đào tạo',
            'description' => 'Hệ thống đào tạo nội bộ, khóa học, tài liệu và kiểm tra kiến thức.',
            'url' => rtrim(env('SSO_APP_LEARNING_URL', 'https://learning.indochinerealestate.vn'), '/'),
            'callback_path' => '/sso/callback',
            'icon' => 'fa-solid fa-graduation-cap',
            'color' => '#7c3aed',
        ],
        'idc' => [
            'key' => 'idc',
            'name' => 'IDC Tool & Dự án',
            'short' => 'IDC Tool',
            'description' => 'Công cụ tra cứu giỏ hàng, mặt bằng, chỉ căn dự án bất động sản.',
            'url' => rtrim(env('SSO_APP_IDC_URL', 'https://idc.indochinerealestate.vn'), '/'),
            'callback_path' => '/sso/callback',
            'icon' => 'fa-solid fa-map-location-dot',
            'color' => '#0891b2',
        ],
    ],
];
