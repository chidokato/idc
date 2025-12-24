<!DOCTYPE html>
<html lang="en">
<head>
<!-- Required Meta Tags Always Come First -->
<base href="{{asset('')}}">
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- Title -->
<title>Dashboard | Front - Admin &amp; Dashboard Template</title>

<!-- Favicon -->
<link rel="shortcut icon" href="favicon.ico">

<!-- Font -->
<link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">

<!-- CSS Implementing Plugins -->
<link rel="stylesheet" href="account/css/vendor.min.css">
<link rel="stylesheet" href="account/vendor/icon-set/style.css">

<!-- CSS Front Template -->
<link rel="stylesheet" href="account/css/theme.min.css?v=1.0">

<link rel="stylesheet" href="account/css/custom.css">

@yield('css')

</head>

<body class="footer-offset @yield('body') ">


<!-- JS Preview mode only -->
<div id="headerMain" class="d-none">

@include('account.layout.header')

</div>

<div id="headerFluid" class="d-none">

</div>

<div id="headerDouble" class="d-none">

</div>

@include('account.layout.menu')

<div id="sidebarCompact" class="d-none">
@include('account.layout.menu1')
</div>

<script src="account/js/demo.js"></script>


<main id="content" role="main" class="main pointer-event">


@yield('content')


@include('account.layout.footer')

</main>

@include('account.layout.popup')

<!-- JS Implementing Plugins -->
<script src="account/js/vendor.min.js"></script>
<script src="account/vendor/chart.js/dist/Chart.min.js"></script>
<script src="account/vendor/chart.js.extensions/chartjs-extensions.js"></script>
<script src="account/vendor/chartjs-plugin-datalabels/dist/chartjs-plugin-datalabels.min.js"></script>

<!-- JS Front -->
<script src="account/js/theme.min.js"></script>
<script src="account/js/custom.js"></script>

<!-- JS Plugins Init. -->

@yield('js')

@include('admin.alert')

</body>
</html>
