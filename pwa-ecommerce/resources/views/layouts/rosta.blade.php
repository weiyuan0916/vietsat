<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <title>@yield('title', 'Tiệm Nhà Duy')</title>
    <meta name="description" content="@yield('meta_description', 'Tiệm Nhà Duy cung cấp nông sản sạch và đặc sản Tây Nguyên chất lượng cao.')">
    <meta name="keywords" content="@yield('meta_keywords', 'Tiệm Nhà Duy, nông sản sạch, cà phê robusta, mắc ca, tiêu đen, bơ sáp')">
    <meta name="robots" content="@yield('meta_robots', 'index,follow')">
    <meta name="author" content="Tiệm Nhà Duy">
    <link rel="canonical" href="@yield('canonical_url', url()->current())">

    <meta property="og:locale" content="vi_VN">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:site_name" content="Tiệm Nhà Duy">
    <meta property="og:title" content="@yield('og_title', trim($__env->yieldContent('title', 'Tiệm Nhà Duy')))">
    <meta property="og:description" content="@yield('og_description', trim($__env->yieldContent('meta_description', 'Tiệm Nhà Duy cung cấp nông sản sạch và đặc sản Tây Nguyên chất lượng cao.')))">
    <meta property="og:url" content="@yield('og_url', url()->current())">
    <meta property="og:image" content="@yield('og_image', asset('rosta/images/tiemnhaduy.svg'))">

    <meta name="twitter:card" content="@yield('twitter_card', 'summary_large_image')">
    <meta name="twitter:title" content="@yield('twitter_title', trim($__env->yieldContent('title', 'Tiệm Nhà Duy')))">
    <meta name="twitter:description" content="@yield('twitter_description', trim($__env->yieldContent('meta_description', 'Tiệm Nhà Duy cung cấp nông sản sạch và đặc sản Tây Nguyên chất lượng cao.')))">
    <meta name="twitter:image" content="@yield('twitter_image', trim($__env->yieldContent('og_image', asset('rosta/images/tiemnhaduy.svg'))))">
    <script type="application/ld+json">
        {
            "@@context": "https://schema.org",
            "@@type": "Organization",
            "name": "Tiệm Nhà Duy",
            "url": "{{ url('/') }}",
            "logo": "{{ asset('rosta/images/tiemnhaduy.svg') }}",
            "email": "support@tiemnhaduy.com",
            "telephone": "+84981314516"
        }
    </script>
    <script type="application/ld+json">
        {
            "@@context": "https://schema.org",
            "@@type": "WebSite",
            "name": "Tiệm Nhà Duy",
            "url": "{{ url('/') }}",
            "inLanguage": "vi-VN"
        }
    </script>
    @stack('structured_data')
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('rosta/images/favicon_io/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('rosta/images/favicon_io/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('rosta/images/favicon_io/favicon-16x16.png') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('rosta/images/favicon_io/favicon.ico') }}">
    <link rel="manifest" href="{{ asset('rosta/images/favicon_io/site.webmanifest') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com/">
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Forum&family=Jost:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link href="{{ asset('rosta/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('rosta/css/slicknav.min.css') }}" rel="stylesheet">
    <link href="{{ asset('rosta/css/swiper-bundle.min.css') }}" rel="stylesheet">
    <link href="{{ asset('rosta/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('rosta/css/animate.css') }}" rel="stylesheet">
    <link href="{{ asset('rosta/css/magnific-popup.css') }}" rel="stylesheet">
    <link href="{{ asset('rosta/css/mousecursor.css') }}" rel="stylesheet">
    <link href="{{ asset('rosta/css/custom.css') }}" rel="stylesheet">
    <style>
        body:not(.is-home-page) header.main-header .header-sticky,
        body:not(.is-home-page) header.main-header .header-sticky.active {
            background: transparent !important;
            border-bottom-color: transparent !important;
            box-shadow: none !important;
        }
        body:not(.is-home-page) .main-menu ul li.nav-item a,
        body:not(.is-home-page) .main-menu ul li.nav-item.submenu > a {
            color: #111 !important;
        }
        body:not(.is-home-page) .header-btn .btn-default,
        body:not(.is-home-page) .header-btn .btn-default.btn-highlighted {
            color: #111 !important;
            border-color: #111 !important;
            background: transparent !important;
        }
        body:not(.is-home-page) .header-btn .btn-default:hover,
        body:not(.is-home-page) .header-btn .btn-default.btn-highlighted:hover {
            color: #fff !important;
            background: #111 !important;
        }
        body:not(.is-home-page) .header-sidebar-btn .btn-popup {
            filter: invert(1);
        }
        body:not(.is-home-page) .navbar-brand img {
            filter: brightness(0) saturate(100%);
        }
        body:not(.is-home-page) .onyx-signature svg #SigAnim path,
        body:not(.is-home-page) .navbar-brand svg #anim path {
            stroke: #111 !important;
            fill: #111 !important;
        }
        body:not(.is-home-page) .slicknav_icon .slicknav_icon-bar {
            background-color: #111 !important;
        }
    </style>
</head>
<body class="onyx-theme {{ request()->routeIs('home') ? 'is-home-page' : 'is-inner-page' }}">
    <a class="skip-link" href="#main-content">Chuyển đến nội dung chính</a>
    @include('pages.partials.rosta.preloader')
    @include('pages.partials.rosta.ticker')
    @include('pages.partials.rosta.header')
    @yield('content')
    @include('pages.partials.rosta.footer')
    @include('pages.partials.rosta.scripts')
</body>
</html>
