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
<!-- Offcanvas Menu -->
<x-menu />

<!-- PWA Install Alert -->
<x-p-w-alert />

<div class="page-content-wrapper">
    <!-- Search Form-->
    <div class="container">
        <div class="search-form pt-3 rtl-flex-d-row-r">
            <form action="#" method="">
                <input class="form-control" type="search" placeholder="Search in Suha">
                <button type="submit"><i class="ti ti-search"></i></button>
            </form>
            <!-- Alternative Search Options -->
            <div class="alternative-search-options">
                <div class="dropdown"><a class="btn btn-primary dropdown-toggle" id="altSearchOption" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ti ti-adjustments-horizontal"></i></a>
                    <!-- Dropdown Menu -->
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="altSearchOption">
                        <li><a class="dropdown-item" href="#"><i class="ti ti-microphone"> </i>Voice</a></li>
                        <li><a class="dropdown-item" href="#"><i class="ti ti-layout-collage"> </i>Image</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Hero Wrapper -->
    <div class="hero-wrapper">
        <div class="container">
            <div class="pt-3">
                <!-- Hero Slides-->
                <div class="hero-slides owl-carousel">
                    <!-- Single Hero Slide-->
                    <div class="single-hero-slide" style="background-image: url('img/bg-img/1.jpg')">
                        <div class="slide-content h-100 d-flex align-items-center">
                            <div class="slide-text">
                                <h4 class="text-white mb-0" data-animation="fadeInUp" data-delay="100ms" data-duration="1000ms">Amazon Echo</h4>
                                <p class="text-white" data-animation="fadeInUp" data-delay="400ms" data-duration="1000ms">3rd Generation, Charcoal</p><a class="btn btn-primary" href="#" data-animation="fadeInUp" data-delay="800ms" data-duration="1000ms">Buy Now</a>
                            </div>
                        </div>
                    </div>
                    <!-- Single Hero Slide-->
                    <div class="single-hero-slide" style="background-image: url('img/bg-img/2.jpg')">
                        <div class="slide-content h-100 d-flex align-items-center">
                            <div class="slide-text">
                                <h4 class="text-white mb-0" data-animation="fadeInUp" data-delay="100ms" data-duration="1000ms">Light Candle</h4>
                                <p class="text-white" data-animation="fadeInUp" data-delay="400ms" data-duration="1000ms">Now only $22</p><a class="btn btn-primary" href="#" data-animation="fadeInUp" data-delay="500ms" data-duration="1000ms">Buy Now</a>
                            </div>
                        </div>
                    </div>
                    <!-- Single Hero Slide-->
                    <div class="single-hero-slide" style="background-image: url('img/bg-img/3.jpg')">
                        <div class="slide-content h-100 d-flex align-items-center">
                            <div class="slide-text">
                                <h4 class="text-white mb-0" data-animation="fadeInUp" data-delay="100ms" data-duration="1000ms">Fancy Chair</h4>
                                <p class="text-white" data-animation="fadeInUp" data-delay="400ms" data-duration="1000ms">3 years warranty</p><a class="btn btn-primary" href="#" data-animation="fadeInUp" data-delay="800ms" data-duration="1000ms">Buy Now</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Catagories -->
    <div class="product-catagories-wrapper py-3">
        <div class="container">
            <div class="row g-2 rtl-flex-d-row-r">
                <!-- Catagory Card -->
                <div class="col-3">
                    <div class="card catagory-card">
                        <div class="card-body px-2"><a href="catagory.html"><img src="img/core-img/woman-clothes.png" alt=""><span>Women's Fashion</span></a></div>
                    </div>
                </div>
                <!-- Catagory Card -->
                <div class="col-3">
                    <div class="card catagory-card">
                        <div class="card-body px-2"><a href="catagory.html"><img src="img/core-img/grocery.png" alt=""><span>Groceries &amp; Pets</span></a></div>
                    </div>
                </div>
                <!-- Catagory Card -->
                <div class="col-3">
                    <div class="card catagory-card">
                        <div class="card-body px-2"><a href="catagory.html"><img src="img/core-img/shampoo.png" alt=""><span>Health &amp; Beauty</span></a></div>
                    </div>
                </div>
                <!-- Catagory Card -->
                <div class="col-3">
                    <div class="card catagory-card">
                        <div class="card-body px-2"><a href="catagory.html"><img src="img/core-img/rowboat.png" alt=""><span>Sports &amp; Outdoors</span></a></div>
                    </div>
                </div>
                <!-- Catagory Card -->
                <div class="col-3">
                    <div class="card catagory-card">
                        <div class="card-body px-2"><a href="catagory.html"><img src="img/core-img/tv-table.png" alt=""><span>Home Appliance</span></a></div>
                    </div>
                </div>
                <!-- Catagory Card -->
                <div class="col-3">
                    <div class="card catagory-card">
                        <div class="card-body px-2"><a href="catagory.html"><img src="img/core-img/beach.png" alt=""><span>Tour &amp; Travels</span></a></div>
                    </div>
                </div>
                <!-- Catagory Card -->
                <div class="col-3">
                    <div class="card catagory-card">
                        <div class="card-body px-2"><a href="catagory.html"><img src="img/core-img/baby-products.png" alt=""><span>Mother &amp; Baby</span></a></div>
                    </div>
                </div>
                <!-- Catagory Card -->
                <div class="col-3">
                    <div class="card catagory-card active">
                        <div class="card-body px-2"><a href="catagory.html"><img src="img/core-img/price-tag.png" alt=""><span>Clearance Sale</span></a></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Flash Sale Slide -->
    <div class="flash-sale-wrapper">
        <div class="container">
            <div class="section-heading d-flex align-items-center justify-content-between rtl-flex-d-row-r">
                <h6 class="d-flex align-items-center rtl-flex-d-row-r"><i class="ti ti-bolt-lightning me-1 text-danger lni-flashing-effect"></i>Cyclone Offer</h6>
                <!-- Please use event time this format: YYYY/MM/DD hh:mm:ss -->
                <ul class="sales-end-timer ps-0 d-flex align-items-center rtl-flex-d-row-r" data-countdown="2025/12/31 14:21:59">
                    <li><span class="days">0</span>d</li>
                    <li><span class="hours">0</span>h</li>
                    <li><span class="minutes">0</span>m</li>
                    <li><span class="seconds">0</span>s</li>
                </ul>
            </div>
            <!-- Flash Sale Slide-->
            <div class="flash-sale-slide owl-carousel">
                <!-- Flash Sale Card -->
                <div class="card flash-sale-card">
                    <div class="card-body"><a href="single-product.html"><img src="img/product/1.png" alt=""><span class="product-title">Black Table Lamp</span>
                            <p class="sale-price">$7.99<span class="real-price">$15</span></p><span class="progress-title">33% Sold</span>
                            <!-- Progress Bar-->
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" style="width: 33%" aria-valuenow="33" aria-valuemin="0" aria-valuemax="100"></div>
                            </div></a></div>
                </div>
                <!-- Flash Sale Card -->
                <div class="card flash-sale-card">
                    <div class="card-body"><a href="single-product.html"><img src="img/product/2.png" alt=""><span class="product-title">Modern Sofa</span>
                            <p class="sale-price">$14<span class="real-price">$21</span></p><span class="progress-title">57% Sold</span>
                            <!-- Progress Bar-->
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" style="width: 57%" aria-valuenow="57" aria-valuemin="0" aria-valuemax="100"></div>
                            </div></a></div>
                </div>
                <!-- Flash Sale Card -->
                <div class="card flash-sale-card">
                    <div class="card-body"><a href="single-product.html"><img src="img/product/3.png" alt=""><span class="product-title">Classic Garden Chair</span>
                            <p class="sale-price">$36<span class="real-price">$49</span></p><span class="progress-title">99% Sold</span>
                            <!-- Progress Bar-->
                            <div class="progress">
                                <div class="progress-bar bg-danger" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                            </div></a></div>
                </div>
                <!-- Flash Sale Card -->
                <div class="card flash-sale-card">
                    <div class="card-body"><a href="single-product.html"><img src="img/product/1.png" alt=""><span class="product-title">Black Table Lamp</span>
                            <p class="sale-price">$7.99<span class="real-price">$15</span></p><span class="progress-title">33% Sold</span>
                            <!-- Progress Bar-->
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" style="width: 33%" aria-valuenow="33" aria-valuemin="0" aria-valuemax="100"></div>
                            </div></a></div>
                </div>
                <!-- Flash Sale Card -->
                <div class="card flash-sale-card">
                    <div class="card-body"><a href="single-product.html"><img src="img/product/2.png" alt=""><span class="product-title">Modern Sofa</span>
                            <p class="sale-price">$14<span class="real-price">$21</span></p><span class="progress-title">57% Sold</span>
                            <!-- Progress Bar-->
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" style="width: 57%" aria-valuenow="57" aria-valuemin="0" aria-valuemax="100"></div>
                            </div></a></div>
                </div>
                <!-- Flash Sale Card -->
                <div class="card flash-sale-card">
                    <div class="card-body"><a href="single-product.html"><img src="img/product/3.png" alt=""><span class="product-title">Classic Garden Chair</span>
                            <p class="sale-price">$36<span class="real-price">$49</span></p><span class="progress-title">99% Sold</span>
                            <!-- Progress Bar-->
                            <div class="progress">
                                <div class="progress-bar bg-danger" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                            </div></a></div>
                </div>
            </div>
        </div>
    </div>
    <!-- Dark Mode -->
    <div class="container">
        <div class="dark-mode-wrapper mt-3 bg-img p-4 p-lg-5">
            <p class="text-white">You can change your display to a dark background using a dark mode.</p>
            <div class="form-check form-switch mb-0">
                <label class="form-check-label text-white h6 mb-0" for="darkSwitch">Switch to Dark Mode</label>
                <input class="form-check-input" id="darkSwitch" type="checkbox" role="switch">
            </div>
        </div>
    </div>
    <!-- Top Products -->
    <div class="top-products-area py-3">
        <div class="container">
            <div class="section-heading d-flex align-items-center justify-content-between dir-rtl">
                <h6>Top Products</h6><a class="btn btn-sm btn-light" href="shop-grid.html">View all<i class="ms-1 ti ti-arrow-right"></i></a>
            </div>
            <div class="row g-2">
                <!-- Product Card -->
                <div class="col-6 col-md-4">
                    <div class="card product-card">
                        <div class="card-body">
                            <!-- Badge--><span class="badge rounded-pill badge-warning">Sale</span>
                            <!-- Wishlist Button--><a class="wishlist-btn" href="#"><i class="ti ti-heart">                       </i></a>
                            <!-- Thumbnail --><a class="product-thumbnail d-block" href="single-product.html"><img class="mb-2" src="img/product/11.png" alt="">
                                <!-- Offer Countdown Timer: Please use event time this format: YYYY/MM/DD hh:mm:ss -->
                                <ul class="offer-countdown-timer d-flex align-items-center shadow-sm" data-countdown="2025/12/31 23:59:59">
                                    <li><span class="days">0</span>d</li>
                                    <li><span class="hours">0</span>h</li>
                                    <li><span class="minutes">0</span>m</li>
                                    <li><span class="seconds">0</span>s</li>
                                </ul></a>
                            <!-- Product Title --><a class="product-title" href="single-product.html">Beach Cap</a>
                            <!-- Product Price -->
                            <p class="sale-price">$13<span>$42</span></p>
                            <!-- Rating -->
                            <div class="product-rating"><i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i></div>
                            <!-- Add to Cart --><a class="btn btn-primary btn-sm" href="#"><i class="ti ti-plus"></i></a>
                        </div>
                    </div>
                </div>
                <!-- Product Card -->
                <div class="col-6 col-md-4">
                    <div class="card product-card">
                        <div class="card-body">
                            <!-- Badge--><span class="badge rounded-pill badge-success">New</span>
                            <!-- Wishlist Button--><a class="wishlist-btn" href="#"><i class="ti ti-heart">                       </i></a>
                            <!-- Thumbnail --><a class="product-thumbnail d-block" href="single-product.html"><img class="mb-2" src="img/product/5.png" alt=""></a>
                            <!-- Product Title --><a class="product-title" href="single-product.html">Wooden Sofa</a>
                            <!-- Product Price -->
                            <p class="sale-price">$74<span>$99</span></p>
                            <!-- Rating -->
                            <div class="product-rating"><i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i></div>
                            <!-- Add to Cart --><a class="btn btn-primary btn-sm" href="#"><i class="ti ti-plus"></i></a>
                        </div>
                    </div>
                </div>
                <!-- Product Card -->
                <div class="col-6 col-md-4">
                    <div class="card product-card">
                        <div class="card-body">
                            <!-- Badge--><span class="badge rounded-pill badge-success">Sale</span>
                            <!-- Wishlist Button--><a class="wishlist-btn" href="#"><i class="ti ti-heart">                       </i></a>
                            <!-- Thumbnail --><a class="product-thumbnail d-block" href="single-product.html"><img class="mb-2" src="img/product/6.png" alt=""></a>
                            <!-- Product Title --><a class="product-title" href="single-product.html">Roof Lamp</a>
                            <!-- Product Price -->
                            <p class="sale-price">$99<span>$113</span></p>
                            <!-- Rating -->
                            <div class="product-rating"><i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i></div>
                            <!-- Add to Cart --><a class="btn btn-primary btn-sm" href="#"><i class="ti ti-plus"></i></a>
                        </div>
                    </div>
                </div>
                <!-- Product Card -->
                <div class="col-6 col-md-4">
                    <div class="card product-card">
                        <div class="card-body">
                            <!-- Badge--><span class="badge rounded-pill badge-danger">-18%</span>
                            <!-- Wishlist Button--><a class="wishlist-btn" href="#"><i class="ti ti-heart">                       </i></a>
                            <!-- Thumbnail --><a class="product-thumbnail d-block" href="single-product.html"><img class="mb-2" src="img/product/9.png" alt="">
                                <!-- Offer Countdown Timer: Please use event time this format: YYYY/MM/DD hh:mm:ss -->
                                <ul class="offer-countdown-timer d-flex align-items-center shadow-sm" data-countdown="2025/12/23 00:21:29">
                                    <li><span class="days">0</span>d</li>
                                    <li><span class="hours">0</span>h</li>
                                    <li><span class="minutes">0</span>m</li>
                                    <li><span class="seconds">0</span>s</li>
                                </ul></a>
                            <!-- Product Title --><a class="product-title" href="single-product.html">Sneaker Shoes</a>
                            <!-- Product Price -->
                            <p class="sale-price">$87<span>$92</span></p>
                            <!-- Rating -->
                            <div class="product-rating"><i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i></div>
                            <!-- Add to Cart --><a class="btn btn-primary btn-sm" href="#"><i class="ti ti-plus"></i></a>
                        </div>
                    </div>
                </div>
                <!-- Product Card -->
                <div class="col-6 col-md-4">
                    <div class="card product-card">
                        <div class="card-body">
                            <!-- Badge--><span class="badge rounded-pill badge-danger">-11%</span>
                            <!-- Wishlist Button--><a class="wishlist-btn" href="#"><i class="ti ti-heart"></i></a>
                            <!-- Thumbnail --><a class="product-thumbnail d-block" href="single-product.html"><img class="mb-2" src="img/product/8.png" alt=""></a>
                            <!-- Product Title --><a class="product-title" href="single-product.html">Wooden Chair</a>
                            <!-- Product Price -->
                            <p class="sale-price">$21<span>$25</span></p>
                            <!-- Rating -->
                            <div class="product-rating"><i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i></div>
                            <!-- Add to Cart --><a class="btn btn-primary btn-sm" href="#"><i class="ti ti-plus"></i></a>
                        </div>
                    </div>
                </div>
                <!-- Product Card -->
                <div class="col-6 col-md-4">
                    <div class="card product-card">
                        <div class="card-body">
                            <!-- Badge--><span class="badge rounded-pill badge-warning">On Sale</span>
                            <!-- Wishlist Button--><a class="wishlist-btn" href="#"><i class="ti ti-heart"></i></a>
                            <!-- Thumbnail --><a class="product-thumbnail d-block" href="single-product.html"><img class="mb-2" src="img/product/4.png" alt=""></a>
                            <!-- Product Title --><a class="product-title" href="single-product.html">Polo Shirts</a>
                            <!-- Product Price -->
                            <p class="sale-price">$38<span>$41</span></p>
                            <!-- Rating -->
                            <div class="product-rating"><i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i></div>
                            <!-- Add to Cart --><a class="btn btn-primary btn-sm" href="#"><i class="ti ti-plus"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- CTA Area -->
    <div class="container">
        <div class="cta-text dir-rtl p-4 p-lg-5">
            <div class="row">
                <div class="col-9">
                    <h5 class="text-white">20% discount on women's care items.</h5><a class="btn btn-primary" href="#">Grab this offer</a>
                </div>
            </div><img src="img/bg-img/make-up.png" alt="">
        </div>
    </div>
    <!-- Weekly Best Sellers-->
    <div class="weekly-best-seller-area py-3">
        <div class="container">
            <div class="section-heading d-flex align-items-center justify-content-between dir-rtl">
                <h6>Weekly Best Sellers</h6><a class="btn btn-sm btn-light" href="shop-list.html">
                    View all<i class="ms-1 ti ti-arrow-right"></i></a>
            </div>
            <div class="row g-2">
                <!-- Weekly Product Card -->
                <div class="col-12">
                    <div class="card horizontal-product-card">
                        <div class="d-flex align-items-center">
                            <div class="product-thumbnail-side">
                                <!-- Thumbnail --><a class="product-thumbnail d-block" href="single-product.html"><img src="img/product/18.png" alt=""></a>
                                <!-- Wishlist  --><a class="wishlist-btn" href="#"><i class="ti ti-heart"></i></a>
                            </div>
                            <div class="product-description">
                                <!-- Product Title --><a class="product-title d-block" href="single-product.html">Nescafe Coffee Jar</a>
                                <!-- Price -->
                                <p class="sale-price"><i class="ti ti-currency-dollar"></i>$64<span>$89</span></p>
                                <!-- Rating -->
                                <div class="product-rating"><i class="ti ti-star-filled"></i>4.88 <span class="ms-1">(39 review)</span></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Weekly Product Card -->
                <div class="col-12">
                    <div class="card horizontal-product-card">
                        <div class="d-flex align-items-center">
                            <div class="product-thumbnail-side">
                                <!-- Thumbnail --><a class="product-thumbnail d-block" href="single-product.html"><img src="img/product/7.png" alt=""></a>
                                <!-- Wishlist  --><a class="wishlist-btn" href="#"><i class="ti ti-heart"></i></a>
                            </div>
                            <div class="product-description">
                                <!-- Product Title --><a class="product-title d-block" href="single-product.html">Modern Office Chair</a>
                                <!-- Price -->
                                <p class="sale-price"><i class="ti ti-currency-dollar"></i>$99<span>$159</span></p>
                                <!-- Rating -->
                                <div class="product-rating"><i class="ti ti-star-filled"></i>4.82 <span class="ms-1">(125 review)</span></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Weekly Product Card -->
                <div class="col-12">
                    <div class="card horizontal-product-card">
                        <div class="d-flex align-items-center">
                            <div class="product-thumbnail-side">
                                <!-- Thumbnail --><a class="product-thumbnail d-block" href="single-product.html"><img src="img/product/12.png" alt=""></a>
                                <!-- Wishlist  --><a class="wishlist-btn" href="#"><i class="ti ti-heart"></i></a>
                            </div>
                            <div class="product-description">
                                <!-- Product Title --><a class="product-title d-block" href="single-product.html">Beach Sunglasses</a>
                                <!-- Price -->
                                <p class="sale-price"><i class="ti ti-currency-dollar"></i>$24<span>$32</span></p>
                                <!-- Rating -->
                                <div class="product-rating"><i class="ti ti-star-filled"></i>4.79 <span class="ms-1">(63 review)</span></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Weekly Product Card -->
                <div class="col-12">
                    <div class="card horizontal-product-card">
                        <div class="d-flex align-items-center">
                            <div class="product-thumbnail-side">
                                <!-- Thumbnail --><a class="product-thumbnail d-block" href="single-product.html"><img src="img/product/17.png" alt=""></a>
                                <!-- Wishlist  --><a class="wishlist-btn" href="#"><i class="ti ti-heart"></i></a>
                            </div>
                            <div class="product-description">
                                <!-- Product Title --><a class="product-title d-block" href="single-product.html">Meow Mix Cat Food</a>
                                <!-- Price -->
                                <p class="sale-price"><i class="ti ti-currency-dollar"></i>$11.49<span>$13</span></p>
                                <!-- Rating -->
                                <div class="product-rating"><i class="ti ti-star-filled"></i>4.78 <span class="ms-1">(7 review)</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Discount Coupon Card-->
    <div class="container">
        <div class="discount-coupon-card p-4 p-lg-5 dir-rtl">
            <div class="d-flex align-items-center">
                <div class="discountIcon"><img class="w-100" src="img/core-img/discount.png" alt=""></div>
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
                                <!-- Thumbnail --><a class="product-thumbnail d-block" href="single-product.html"><img src="img/product/14.png" alt=""></a>
                            </div>
                            <div class="product-description">
                                <!-- Product Title --><a class="product-title d-block" href="single-product.html">Blue Skateboard</a>
                                <!-- Price -->
                                <p class="sale-price">$39<span>$89</span></p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Featured Product Card-->
                <div class="col-4">
                    <div class="card featured-product-card">
                        <div class="card-body">
                            <!-- Badge--><span class="badge badge-warning custom-badge"><i class="ti ti-star-filled"></i></span>
                            <div class="product-thumbnail-side">
                                <!-- Thumbnail --><a class="product-thumbnail d-block" href="single-product.html"><img src="img/product/15.png" alt=""></a>
                            </div>
                            <div class="product-description">
                                <!-- Product Title --><a class="product-title d-block" href="single-product.html">Travel Bag</a>
                                <!-- Price -->
                                <p class="sale-price">$14.7<span>$21</span></p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Featured Product Card-->
                <div class="col-4">
                    <div class="card featured-product-card">
                        <div class="card-body">
                            <!-- Badge--><span class="badge badge-warning custom-badge"><i class="ti ti-star-filled"></i></span>
                            <div class="product-thumbnail-side">
                                <!-- Thumbnail --><a class="product-thumbnail d-block" href="single-product.html"><img src="img/product/16.png" alt=""></a>
                            </div>
                            <div class="product-description">
                                <!-- Product Title --><a class="product-title d-block" href="single-product.html">Cotton T-shirts</a>
                                <!-- Price -->
                                <p class="sale-price">$3.69<span>$5</span></p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Featured Product Card-->
                <div class="col-4">
                    <div class="card featured-product-card">
                        <div class="card-body">
                            <!-- Badge--><span class="badge badge-warning custom-badge"><i class="ti ti-star-filled"></i></span>
                            <div class="product-thumbnail-side">
                                <!-- Thumbnail --><a class="product-thumbnail d-block" href="single-product.html"><img src="img/product/21.png" alt=""></a>
                            </div>
                            <div class="product-description">
                                <!-- Product Title --><a class="product-title d-block" href="single-product.html">ECG Rice Cooker</a>
                                <!-- Price -->
                                <p class="sale-price">$9.33<span>$13</span></p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Featured Product Card-->
                <div class="col-4">
                    <div class="card featured-product-card">
                        <div class="card-body">
                            <!-- Badge--><span class="badge badge-warning custom-badge"><i class="ti ti-star-filled"></i></span>
                            <div class="product-thumbnail-side">
                                <!-- Thumbnail --><a class="product-thumbnail d-block" href="single-product.html"><img src="img/product/20.png" alt=""></a>
                            </div>
                            <div class="product-description">
                                <!-- Product Title --><a class="product-title d-block" href="single-product.html">Beauty Cosmetics</a>
                                <!-- Price -->
                                <p class="sale-price">$5.99<span>$8</span></p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Featured Product Card-->
                <div class="col-4">
                    <div class="card featured-product-card">
                        <div class="card-body">
                            <!-- Badge--><span class="badge badge-warning custom-badge"><i class="ti ti-star-filled"></i></span>
                            <div class="product-thumbnail-side">
                                <!-- Thumbnail --><a class="product-thumbnail d-block" href="single-product.html"><img src="img/product/19.png" alt=""></a>
                            </div>
                            <div class="product-description">
                                <!-- Product Title --><a class="product-title d-block" href="single-product.html">Basketball</a>
                                <!-- Price -->
                                <p class="sale-price">$16<span>$20</span></p>
                            </div>
                        </div>
                    </div>
                </div>
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
                <div class="card collection-card"><a href="single-product.html"><img src="img/product/17.jpg" alt=""></a>
                    <div class="collection-title"><span>Women</span><span class="badge bg-danger">9</span></div>
                </div>
                <!-- Collection Card-->
                <div class="card collection-card"><a href="single-product.html"><img src="img/product/19.jpg" alt=""></a>
                    <div class="collection-title"><span>Men</span><span class="badge bg-danger">29</span></div>
                </div>
                <!-- Collection Card-->
                <div class="card collection-card"><a href="single-product.html"><img src="img/product/21.jpg" alt=""></a>
                    <div class="collection-title"><span>Kids</span><span class="badge bg-danger">4</span></div>
                </div>
                <!-- Collection Card-->
                <div class="card collection-card"><a href="single-product.html"><img src="img/product/22.jpg" alt=""></a>
                    <div class="collection-title"><span>Gadget</span><span class="badge bg-danger">11</span></div>
                </div>
                <!-- Collection Card-->
                <div class="card collection-card"><a href="single-product.html"><img src="img/product/23.jpg" alt=""></a>
                    <div class="collection-title"><span>Foods</span><span class="badge bg-danger">2</span></div>
                </div>
                <!-- Collection Card-->
                <div class="card collection-card"><a href="single-product.html"><img src="img/product/24.jpg" alt=""></a>
                    <div class="collection-title"><span>Sports</span><span class="badge bg-danger">5</span></div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Internet Connection Status-->
<div class="internet-connection-status" id="internetStatus"></div>
<!-- Footer Nav-->
<div class="footer-nav-area" id="footerNav">
    <div class="suha-footer-nav">
        <ul class="h-100 d-flex align-items-center justify-content-between ps-0 d-flex rtl-flex-d-row-r">
            <li><a href="home.html"><i class="ti ti-home"></i>Home</a></li>
            <li><a href="message.html"><i class="ti ti-message"></i>Chat</a></li>
            <li><a href="cart.html"><i class="ti ti-basket"></i>Cart</a></li>
            <li><a href="settings.html"><i class="ti ti-settings"></i>Settings</a></li>
            <li><a href="pages.html"><i class="ti ti-heart"></i>Pages</a></li>
        </ul>
    </div>
</div>
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
