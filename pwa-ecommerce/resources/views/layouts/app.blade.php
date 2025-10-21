<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, viewport-fit=cover, shrink-to-fit=no">
    <meta name="description" content="@yield('meta_description', config('app.name') . ' - E-commerce Platform')">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="theme-color" content="#625AFA">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- SEO Meta Tags -->
    <meta name="keywords" content="@yield('meta_keywords', 'ecommerce, vendors, products, shop')">
    <meta name="author" content="{{ config('app.name') }}">
    <link rel="canonical" href="{{ url()->current() }}">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('og_title', config('app.name'))">
    <meta property="og:description" content="@yield('og_description', config('app.name') . ' - E-commerce Platform')">
    <meta property="og:image" content="@yield('og_image', asset('frontend/img/core-img/logo-small.png'))">
    
    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="@yield('twitter_title', config('app.name'))">
    <meta property="twitter:description" content="@yield('twitter_description', config('app.name') . ' - E-commerce Platform')">
    <meta property="twitter:image" content="@yield('twitter_image', asset('frontend/img/core-img/logo-small.png'))">
    
    <!-- Title -->
    <title>@yield('title', config('app.name') . ' - E-commerce Platform')</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('frontend/img/icons/icon-72x72.png') }}">
    
    <!-- Apple Touch Icon -->
    <link rel="apple-touch-icon" href="{{ asset('frontend/img/icons/icon-96x96.png') }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('frontend/img/icons/icon-152x152.png') }}">
    <link rel="apple-touch-icon" sizes="167x167" href="{{ asset('frontend/img/icons/icon-167x167.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('frontend/img/icons/icon-180x180.png') }}">
    
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('frontend/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/css/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/css/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/css/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/css/magnific-popup.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/css/nice-select.css') }}">
    
    <!-- Stylesheet -->
    <link rel="stylesheet" href="{{ asset('frontend/style.css') }}">
    
    <!-- Web App Manifest -->
    <link rel="manifest" href="{{ asset('frontend/manifest.json') }}">
    
    @stack('styles')
</head>
<body>
    <!-- Preloader -->
    <x-preloader />
    
    <!-- Header Area -->
    <x-page-header />
    
    <!-- Offcanvas Menu -->
    <x-menu />
    
    <!-- PWA Install Alert -->
    <x-p-w-alert />
    
    <!-- Page Content -->
    <div class="page-content-wrapper">
        @yield('content')
    </div>
    
    <!-- Internet Connection Status -->
    <div class="internet-connection-status" id="internetStatus"></div>
    
    <!-- Footer Nav -->
    <x-footer-nav />
    
    <!-- All JavaScript Files -->
    <script src="{{ asset('frontend/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('frontend/js/jquery.min.js') }}"></script>
    <script src="{{ asset('frontend/js/waypoints.min.js') }}"></script>
    <script src="{{ asset('frontend/js/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('frontend/js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('frontend/js/jquery.magnific-popup.min.js') }}"></script>
    <script src="{{ asset('frontend/js/jquery.counterup.min.js') }}"></script>
    <script src="{{ asset('frontend/js/jquery.countdown.min.js') }}"></script>
    <script src="{{ asset('frontend/js/jquery.passwordstrength.js') }}"></script>
    <script src="{{ asset('frontend/js/jquery.nice-select.min.js') }}"></script>
    <script src="{{ asset('frontend/js/theme-switching.js') }}"></script>
    <script src="{{ asset('frontend/js/no-internet.js') }}"></script>
    <script src="{{ asset('frontend/js/active.js') }}"></script>
    <script src="{{ asset('frontend/js/pwa.js') }}"></script>
    
    @stack('scripts')
</body>
</html>

