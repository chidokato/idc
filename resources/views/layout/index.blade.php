<!doctype html>
<html>
<head>
    <!-- seo -->
    <base href="{{asset('')}}">
    <title>@yield('title')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
    <meta name="description" content="@yield('description')"/>
    <meta name="keywords" itemprop="keywords" content="@yield('keywords')" />
    <meta name="news_keywords" content="@yield('keywords')" />
    <meta name="robots" content="@yield('robots')"/>
    <link rel="shortcut icon" href="data/images/{{$setting->favicon}}" />
    <link rel="canonical" href="@yield('url')"/>
    <link rel="alternate" href="{{asset('')}}" hreflang="vi-vn" />
    <!-- and seo -->
    <!-- og -->
    <meta property="og:locale" content="vi_VN" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="@yield('title')" />
    <meta property="og:description" content="@yield('description')" />
    <meta property="og:url" content="@yield('url')"/>
    <meta property="og:site_name" content="site_name"/>
    <meta property="og:images" content="@yield('img')"/>
    <meta property="og:image" content="@yield('img')"/>
    <meta property="og:image:alt" content="@yield('title')" />
    <!-- and og -->
    <!-- twitter -->
    <meta name="twitter:card" content="summary" />
    <meta name="twitter:title" content="@yield('title')" />
    <meta name="twitter:description" content="@yield('description')" />
    <!-- and twitter -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0,user-scalable=0"/>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta property="article:author" content="admin" />

    <!-- CSS ========================= -->
    <!------------------- CSS ------------------->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/swiper-bundle.min.css" rel="stylesheet">
    <link href="assets/css/fonts.css" rel="stylesheet">
    <link href="assets/css/common.css" rel="stylesheet">
    <link href="assets/css/header.css" rel="stylesheet">
    <link href="assets/css/footer.css" rel="stylesheet">
    <link href="assets/css/sort.css" rel="stylesheet">
    <link href="assets/css/card.css" rel="stylesheet">
    <link href="assets/css/form.css" rel="stylesheet">
    <link href="assets/css/runglac.css" rel="stylesheet">
    <link href="assets/css/simpleLightbox.css" rel="stylesheet">
    <link href="assets/css/popup.css" rel="stylesheet">
    <link href="assets/css/responsive.css" rel="stylesheet">
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-8R10SSS97Z"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'G-8R10SSS97Z');
    </script>
    @yield('css')
    
    {!! $setting->header !!}
</head>

<body>
    @include('layout.header')

    @yield('content')
    
    @include('layout.footer')

    
    <!-- JS
    ============================================ -->
    <!------------------- JS core------------------->
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script src="http://code.jquery.com/jquery-latest.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/swiper-bundle.min.js"></script>
    <script src="assets/js/simpleLightbox.min.js"></script>
    <script src="assets/js/custom.js?v=30"></script>
    <script src="assets/js/popup.js?v=30"></script>
    
    <script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js" type="text/javascript"></script>
    <script src="assets/js/validate.js"></script>
    
    @yield('js')

    @include('admin.alert')

    {!! $setting->footer !!}
</body>

</html>