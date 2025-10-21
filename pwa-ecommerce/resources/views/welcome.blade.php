<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, viewport-fit=cover, shrink-to-fit=no">
    <meta name="description" content="Suha - Multipurpose E-commerce Mobile HTML Template">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="theme-color" content="#625AFA">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <!-- The above tags *must* come first in the head, any other head content must come *after* these tags -->
    <!-- Title -->
    <title>Soda - E-commerce</title>
    <link rel="preconnect" href="https://fonts.googleapis.com/">
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&amp;display=swap" rel="stylesheet">
    <!-- Favicon -->
    <link rel="icon" href="img/icons/icon-72x72.png">
    <!-- Apple Touch Icon -->
    <link rel="apple-touch-icon" href="img/icons/icon-96x96.png">
    <link rel="apple-touch-icon" sizes="152x152" href="img/icons/icon-152x152.png">
    <link rel="apple-touch-icon" sizes="167x167" href="img/icons/icon-167x167.png">
    <link rel="apple-touch-icon" sizes="180x180" href="img/icons/icon-180x180.png">
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
    <link rel="manifest" href="{{ asset('manifest.json') }}">
</head>
<body>
<!-- Preloader-->
<x-preloader />
<!-- Header Area -->
<x-header />
{{--<x-page-header />--}}
<!-- Offcanvas Menu -->
<x-menu />

<!-- PWA Install Alert -->
<x-p-w-alert />

<div class="page-content-wrapper">
    <!-- Search Form-->
    <x-search-form />

    <!-- Hero Wrapper -->
    <x-main-slider />
    <!-- Product Catagories -->
    <x-main-category />
    <!-- Flash Sale Slide -->
    <x-flash-sale />
    <!-- Dark Mode -->
    <x-main-dark-mode />
    <!-- Top Products -->
    <x-top-product-section />
    <!-- CTA Area -->
    <x-promo-banner />
    <!-- Weekly Best Sellers-->
    <x-best-seller-list />
    <!-- Discount Coupon Card-->
    <div class="container">
        <div class="discount-coupon-card p-4 p-lg-5 dir-rtl">
            <div class="d-flex align-items-center">
                <div class="discountIcon"><img class="w-100" src="{{ asset('frontend/img/core-img/discount.png') }}" alt=""></div>
                <div class="text-content">
                    <h5 class="text-white mb-2">Get 20% discount!</h5>
                    <p class="text-white mb-0">To get discount, enter the<span class="px-1 fw-bold">GET20</span>code on the checkout page.</p>
                </div>
            </div>
        </div>
    </div>
    <!-- Featured Products Wrapper-->
    <div class="featured-products-wrapper py-3">
        <div class="container">
            <div class="section-heading d-flex align-items-center justify-content-between dir-rtl">
                <h6>Featured Products</h6><a class="btn btn-sm btn-light" href="featured-products.html">View all<i class="ms-1 ti ti-arrow-right"></i></a>
            </div>
            <div class="row g-2">
                <!-- Featured Product Card-->
                <div class="col-4">
                    <div class="card featured-product-card">
                        <div class="card-body">
                            <!-- Badge--><span class="badge badge-warning custom-badge"><i class="ti ti-star-filled"></i></span>
                            <div class="product-thumbnail-side">
                                <!-- Thumbnail --><a class="product-thumbnail d-block" href="single-product.html"><img src="{{ asset('frontend/img/product/14.png') }}" alt=""></a>
                            </div>
                            <div class="product-description">
                                <!-- Product Title --><a class="product-title d-block" href="single-product.html">Blue Skateboard</a>
                                <!-- Price -->
                                <p class="sale-price">$39<span>$89</span></p>
                            </div>
                        </div>
                    </div>
                </div>
                @for($i=1 ; $i<=4 ; $i++)
                    <!-- Featured Product Card-->
                    <div class="col-4">
                        <div class="card featured-product-card">
                            <div class="card-body">
                                <!-- Badge--><span class="badge badge-warning custom-badge"><i class="ti ti-star-filled"></i></span>
                                <div class="product-thumbnail-side">
                                    <!-- Thumbnail --><a class="product-thumbnail d-block" href="single-product.html"><img style="max-width: 150px; max-height: 150px" src="{{ "https://scontent.fsgn2-4.fna.fbcdn.net/v/t39.30808-6/543426029_738700995826691_3730733313910458830_n.jpg?stp=cp6_dst-jpg_tt6&_nc_cat=101&ccb=1-7&_nc_sid=aa7b47&_nc_eui2=AeGp_LHB13qFgoyEb1Oz0f9_Nz1K_ZesS743PUr9l6xLvlUfehS5hG8br-LKUImFKNvxuG8xR66J4iPYDIK9s2AG&_nc_ohc=lRejanzIqJgQ7kNvwG9CREy&_nc_oc=AdkUM5D4Izvlxs7lk7ll6uSwIp3PuRmKJi4IRXFNZKb_TRSmKD8qgEmANVxtZmUBnob6jftn33_Yl49cdHyGUfFP&_nc_zt=23&_nc_ht=scontent.fsgn2-4.fna&_nc_gid=0HgTZ45m9W5zBFPSYxRysg&oh=00_AfZnZMc3P3h_uRgrW2gGmCP_CWzdZ53RlXY_PhsdP6sAJA&oe=68C3C5DD" ?? asset('frontend/img/product/15.png') }}" alt=""></a>
                                </div>
                                <div class="product-description">
                                    <!-- Product Title --><a class="product-title d-block" href="single-product.html">Travel Bag</a>
                                    <!-- Price -->
                                    <p class="sale-price">$14.7<span>$21</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endfor

            </div>
        </div>
    </div>

    <div class="pb-3">
        <div class="container">
            <div class="section-heading d-flex align-items-center justify-content-between dir-rtl">
                <h6>Collections</h6><a class="btn btn-sm btn-light" href="#">
                    View all<i class="ms-1 ti ti-arrow-right"></i></a>
            </div>
            <!-- Collection Slide-->
            <div class="collection-slide owl-carousel">
                <!-- Collection Card-->
                <div class="card collection-card"><a href="single-product.html"><img style="max-width: 151px; max-height: 188px" src="{{ "https://scontent.fsgn2-4.fna.fbcdn.net/v/t39.30808-6/543426029_738700995826691_3730733313910458830_n.jpg?stp=cp6_dst-jpg_tt6&_nc_cat=101&ccb=1-7&_nc_sid=aa7b47&_nc_eui2=AeGp_LHB13qFgoyEb1Oz0f9_Nz1K_ZesS743PUr9l6xLvlUfehS5hG8br-LKUImFKNvxuG8xR66J4iPYDIK9s2AG&_nc_ohc=lRejanzIqJgQ7kNvwG9CREy&_nc_oc=AdkUM5D4Izvlxs7lk7ll6uSwIp3PuRmKJi4IRXFNZKb_TRSmKD8qgEmANVxtZmUBnob6jftn33_Yl49cdHyGUfFP&_nc_zt=23&_nc_ht=scontent.fsgn2-4.fna&_nc_gid=0HgTZ45m9W5zBFPSYxRysg&oh=00_AfZnZMc3P3h_uRgrW2gGmCP_CWzdZ53RlXY_PhsdP6sAJA&oe=68C3C5DD" ?? asset('frontend/img/product/17.jpg') }}" alt=""></a>
                    <div class="collection-title"><span>Women</span><span class="badge bg-danger">9</span></div>
                </div>

                </div>
            </div>
        </div>
    </div>
</div>
<!-- Internet Connection Status-->
<div class="internet-connection-status" id="internetStatus"></div>
<!-- Footer Nav-->
<x-footer />
<!-- All JavaScript Files-->
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
</body>

</html>
