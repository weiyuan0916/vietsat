@extends('layouts.rosta')

@section('title', 'Tiệm Nhà Duy')
@section('meta_description', 'Trang chủ theo giao diện index-video của rosta.')
@section('og_image', asset('rosta/images/tiemnhaduy.svg'))
@section('canonical_url', route('home'))

@push('head_preloads')
<link rel="preload" as="image" href="https://cdn.shopify.com/s/files/1/1707/3261/files/hp-poster.webp?v=1736778184" fetchpriority="high" type="image/webp">
@endpush

@push('structured_data')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "WebPage",
    "name": "Tiệm Nhà Duy",
    "description": "Trang chủ theo giao diện index-video của rosta.",
    "url": "{{ route('home') }}",
    "inLanguage": "vi-VN"
}
</script>
@endpush

@section('content')

    <!-- Hero Section Start -->
    <div class="hero hero-video" id="main-content">
        <div class="background">
            <img
                src="https://cdn.shopify.com/s/files/1/1707/3261/files/hp-poster.webp?v=1736778184"
                alt="Nông sản sạch Tiệm Nhà Duy"
                fetchpriority="high"
                loading="eager"
                decoding="async"
                width="1920"
                height="1080"
                aria-hidden="true"
                style="position:absolute;top:0;left:0;width:100%;height:100%;object-fit:cover;"
            >
            <video preload="metadata" autoplay loop muted playsinline poster="https://cdn.shopify.com/s/files/1/1707/3261/files/hp-poster.webp?v=1736778184">
                <source type="video/mp4" src="https://cdn.shopify.com/videos/c/o/v/f3a0b38123db492c8ddea379bb7e7474.mp4">
            </video>
        </div>
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8 col-md-10">
                    <!-- Hero Content Start -->
                    <div class="hero-content">
                        <!-- Section Title Start -->
                        <div class="section-title">
                            <h3 class="wow fadeInUp">nông sản sạch từ tâm, chất lượng đến tay bạn</h3>
                            <h1 class="text-anime-style-3" data-cursor="-opaque">Robusta Gia Lai</h1>
                            <p class="wow fadeInUp" data-wow-delay="0.2s">Tiệm Nhà Duy giới thiệu đặc sản Tây Nguyên gồm cà phê robusta, hạt mắc ca béo bùi, tiêu đen nồng thơm và bơ sáp tươi ngon. Chúng tôi tập trung nguồn gốc rõ ràng, hương vị thật và giá trị bền vững cho mỗi gia đình Việt.</p>
                        </div>
                        <!-- Section Title End -->
                        
                        <!-- Hero Button Start -->
                        <div class="hero-btn wow fadeInUp" data-wow-delay="0.4s">
                            <a href="{{ route('about') }}" class="btn-default">Khám phá sản phẩm</a>
                            <a href="{{ route('book-table') }}" class="btn-default btn-highlighted">Thanh toán ngay</a>
                        </div>
                        <!-- Hero Button End -->
                    </div>
                    <!-- Hero Content End -->
                </div>
            </div>
        </div>
    </div>
    <!-- Hero Section End -->

    <!-- Scrolling Ticker Section Start -->
    <div class="our-scrolling-ticker">
        <!-- Scrolling Ticker Start -->
        <div class="scrolling-ticker-box">
            <div class="scrolling-content">
                <span><img src="{{ asset('rosta/images/asterisk-icon.svg') }}" alt="Decorative divider icon">Espresso</span>
                <span><img src="{{ asset('rosta/images/asterisk-icon.svg') }}" alt="Decorative divider icon">Americano</span>
                <span><img src="{{ asset('rosta/images/asterisk-icon.svg') }}" alt="Decorative divider icon">Latte</span>
                <span><img src="{{ asset('rosta/images/asterisk-icon.svg') }}" alt="Decorative divider icon">Cappuccino</span>
                <span><img src="{{ asset('rosta/images/asterisk-icon.svg') }}" alt="Decorative divider icon">Mocha</span>
                <span><img src="{{ asset('rosta/images/asterisk-icon.svg') }}" alt="Decorative divider icon">Macchiato</span>
                <span><img src="{{ asset('rosta/images/asterisk-icon.svg') }}" alt="Decorative divider icon">Cold Brew</span>
                <span><img src="{{ asset('rosta/images/asterisk-icon.svg') }}" alt="Decorative divider icon">Espresso</span>
                <span><img src="{{ asset('rosta/images/asterisk-icon.svg') }}" alt="Decorative divider icon">Americano</span>
                <span><img src="{{ asset('rosta/images/asterisk-icon.svg') }}" alt="Decorative divider icon">Latte</span>
                <span><img src="{{ asset('rosta/images/asterisk-icon.svg') }}" alt="Decorative divider icon">Cappuccino</span>
                <span><img src="{{ asset('rosta/images/asterisk-icon.svg') }}" alt="Decorative divider icon">Mocha</span>
                <span><img src="{{ asset('rosta/images/asterisk-icon.svg') }}" alt="Decorative divider icon">Macchiato</span>
                <span><img src="{{ asset('rosta/images/asterisk-icon.svg') }}" alt="Decorative divider icon">Cold Brew</span>
            </div>

            <div class="scrolling-content">
                <span><img src="{{ asset('rosta/images/asterisk-icon.svg') }}" alt="Decorative divider icon">Espresso</span>
                <span><img src="{{ asset('rosta/images/asterisk-icon.svg') }}" alt="Decorative divider icon">Americano</span>
                <span><img src="{{ asset('rosta/images/asterisk-icon.svg') }}" alt="Decorative divider icon">Latte</span>
                <span><img src="{{ asset('rosta/images/asterisk-icon.svg') }}" alt="Decorative divider icon">Cappuccino</span>
                <span><img src="{{ asset('rosta/images/asterisk-icon.svg') }}" alt="Decorative divider icon">Mocha</span>
                <span><img src="{{ asset('rosta/images/asterisk-icon.svg') }}" alt="Decorative divider icon">Macchiato</span>
                <span><img src="{{ asset('rosta/images/asterisk-icon.svg') }}" alt="Decorative divider icon">Cold Brew</span>
                <span><img src="{{ asset('rosta/images/asterisk-icon.svg') }}" alt="Decorative divider icon">Espresso</span>
                <span><img src="{{ asset('rosta/images/asterisk-icon.svg') }}" alt="Decorative divider icon">Americano</span>
                <span><img src="{{ asset('rosta/images/asterisk-icon.svg') }}" alt="Decorative divider icon">Latte</span>
                <span><img src="{{ asset('rosta/images/asterisk-icon.svg') }}" alt="Decorative divider icon">Cappuccino</span>
                <span><img src="{{ asset('rosta/images/asterisk-icon.svg') }}" alt="Decorative divider icon">Mocha</span>
                <span><img src="{{ asset('rosta/images/asterisk-icon.svg') }}" alt="Decorative divider icon">Macchiato</span>
                <span><img src="{{ asset('rosta/images/asterisk-icon.svg') }}" alt="Decorative divider icon">Cold Brew</span>
            </div>
        </div>
        <!-- Scrolling Ticker End -->
    </div>
    <!-- Scrolling Ticker Section End -->

    <!-- About us Section Start -->
    <div class="about-us">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <!-- About us Content Start -->
                    <div class="about-us-content">
                        <!-- Section Title Start -->
                        <div class="section-title">
                            <h3 class="wow fadeInUp">về chúng tôi</h3>
                            <h2 class="text-anime-style-3" data-cursor="-opaque">Kết nối gia đình Việt qua từng sản phẩm sạch mỗi ngày</h2>
                        </div>
                        <!-- Section Title End -->
                         
                        <!-- About Body List Start -->
                        <div class="about-body-list">
                            <!-- About Body Item Start -->
                            <div class="about-body-item wow fadeInUp" data-wow-delay="0.2s">
                                <div class="icon-box">
                                    <img src="{{ asset('rosta/images/icon-about-body-item-1.svg') }}" alt="About Rosta feature icon">
                                </div>
                                <div class="about-body-list-content">
                                    <h3>Giao hàng toàn quốc</h3>
                                    <p>Đặt mua nhanh các sản phẩm nông sản như cà phê robusta, mắc ca, tiêu và bơ với quy trình đóng gói kỹ, giao hàng tận nơi.</p>
                                </div>
                            </div>
                            <!-- About Body Item End -->
                            
                            <!-- About Body Item Start -->
                            <div class="about-body-item wow fadeInUp" data-wow-delay="0.4s">
                                <div class="icon-box">
                                    <img src="{{ asset('rosta/images/icon-about-body-item-2.svg') }}" alt="About Rosta feature icon">
                                </div>
                                <div class="about-body-list-content">
                                    <h3>Quà tặng và sự kiện</h3>
                                    <p>Chúng tôi nhận tư vấn combo quà tặng nông sản cho doanh nghiệp, gia đình và các dịp lễ tết với hình ảnh chỉn chu.</p>
                                </div>
                            </div>
                            <!-- About Body Item End -->
                        </div>
                        <!-- About Body List End -->
                        
                        <!-- About Us Footer Start -->
                        <div class="about-us-footer wow fadeInUp" data-wow-delay="0.6s">
                            <!-- About Button Start -->
                            <div class="about-btn">
                                <a href="{{ route('about') }}" class="btn-default">Xem thêm về chúng tôi</a>
                            </div>
                            <!-- About Button End -->
                            
                            <!-- Video Play Button Start -->
                            <div class="video-play-button">
                                <a href="https://www.youtube.com/watch?v=Y-x0efG1seA" class="popup-video" data-cursor-text="Play">
                                    <i class="fa-solid fa-play"></i>
                                </a>
                                <p>xem video</p>
                            </div>
                            <!-- Video Play Button End -->
                        </div>
                        <!-- About Us Footer End -->
                    </div>
                    <!-- About us Content End -->
                </div>

                <div class="col-lg-6">
                    <!-- About Us Image Start -->
                    <div class="about-us-image">
                        <!-- About Us Image Start -->
                        <div class="about-us-img">
                            <figure class="image-anime">
                                <video preload="none" id="video_block_096b8d46-578c-4cff-924a-066c60ffabbb" play-on-visible="" autoplay loop muted playsinline poster="https://product.onyxcontent.com/media/pages/ecom/home/12c54db84b-1776176788/screenshot.webp" class="visible">
                                    <source type="video/mp4" src="https://product.onyxcontent.com/media/pages/ecom/home/cfa1f1e98e-1776176712/colombiabrewinghomepagevideo.mp4">
                                </video>
                            </figure>
                        </div>
                        <!-- About Us Image End -->
                        
                        <!-- Opening Time Box Start -->
                        <div class="opening-time-box">
                            <!-- Icon Box Start -->
                            <div class="icon-box">
                                <i class="fa-regular fa-clock"></i>
                            </div>
                            <!-- Icon Box End -->
                            
                            <!-- Opening Time Content Start -->
                            <div class="opening-time-content">
                                <h3>Giờ hoạt động</h3>
                                <ul>
                                    <li>Thứ Hai - Thứ Sáu<span>09:30 - 19:30</span></li>
                                    <li>Thu Bay<span>10:30 - 17:00</span></li>
                                    <li>Chủ Nhật<span>Hỗ trợ trực tuyến</span></li>
                                </ul>
                            </div>
                            <!-- Opening Time Content End -->
                        </div>
                        <!-- About Menu Box End -->
                    </div>
                    <!-- Opening Time Box End -->
                </div>
            </div>
        </div>
    </div>
    <!-- About us Section End -->

    <!-- Why Choose Us Section Start -->
    <div class="why-choose-us light-bg-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <!-- Why Choose Content Start -->
                    <div class="why-choose-content">
                        <!-- Section Title Start -->
                        <div class="section-title">
                            <h3 class="wow fadeInUp">Lý do chọn chúng tôi</h3>
                            <h2 class="text-anime-style-3" data-cursor="-opaque">Nơi hương vị nông sản Tây Nguyên gặp sự tận tâm</h2>
                        </div>
                        <!-- Section Title End -->

                        <!-- Why Choose Button Start -->
                        <div class="why-choose-btn wow fadeInUp" data-wow-delay="0.2s">
                            <a href="{{ route('contact') }}" class="btn-default">Liên hệ ngay</a>
                        </div>
                        <!-- Why Choose Button End -->
                    </div>
                    <!-- Why Choose Content End -->
                </div>

                <div class="col-lg-6">
                    <!-- Why Choose List Start -->
                    <div class="why-choose-list wow fadeInUp" data-wow-delay="0.2s">
                        <!-- Why Choose Item Start -->
                        <div class="why-choose-item">
                            <div class="icon-box">
                                <img src="{{ asset('rosta/images/icon-why-choose-1.svg') }}" alt="Rosta service feature icon">
                            </div>
                            <div class="why-choose-item-content">
                                <h3>Nguồn gốc rõ ràng</h3>
                                <p>Cà phê Robusta Gia Lai và các nông sản đều được chọn lọc kỹ, ưu tiên chất lượng thật.</p>
                            </div>
                        </div>
                        <!-- Why Choose Item End -->

                        <!-- Why Choose Item Start -->
                        <div class="why-choose-item">
                            <div class="icon-box">
                                <img src="{{ asset('rosta/images/icon-why-choose-2.svg') }}" alt="Rosta service feature icon">
                            </div>
                            <div class="why-choose-item-content">
                                <h3>Sản phẩm tươi mới</h3>
                                <p>Mắc ca, tiêu và bơ được thu mua theo mùa vụ, giữ độ tươi ngon và hương vị tự nhiên.</p>
                            </div>
                        </div>
                        <!-- Why Choose Item End -->

                        <!-- Why Choose Item Start -->
                        <div class="why-choose-item">
                            <div class="icon-box">
                                <img src="{{ asset('rosta/images/icon-why-choose-3.svg') }}" alt="Rosta service feature icon">
                            </div>
                            <div class="why-choose-item-content">
                                <h3>Đóng gói sạch đẹp</h3>
                                <p>Phù hợp dùng gia đình hoặc làm quà biếu tặng với hình thức gọn gàng, lịch sự.</p>
                            </div>
                        </div>
                        <!-- Why Choose Item End -->

                        <!-- Why Choose Item Start -->
                        <div class="why-choose-item">
                            <div class="icon-box">
                                <img src="{{ asset('rosta/images/icon-why-choose-4.svg') }}" alt="Rosta service feature icon">
                            </div>
                            <div class="why-choose-item-content">
                                <h3>Tư vấn nhanh</h3>
                                <p>Hỗ trợ chọn sản phẩm phù hợp nhu cầu sử dụng, pha chế và bảo quản cho khách hàng mới.</p>
                            </div>
                        </div>
                        <!-- Why Choose Item End -->


                        <!-- Why Choose Item Start -->
                        <div class="why-choose-item">
                            <div class="icon-box">
                                <img src="{{ asset('rosta/images/icon-why-choose-5.svg') }}" alt="Rosta service feature icon">
                            </div>
                            <div class="why-choose-item-content">
                                <h3>Thân thiện, dễ tiếp cận</h3>
                                <p>Nội dung dễ hiểu, minh bạch giá và hướng dẫn cụ thể để bạn yên tâm khi mua.</p>
                            </div>
                        </div>
                        <!-- Why Choose Item End -->


                        <!-- Why Choose Item Start -->
                        <div class="why-choose-item">
                            <div class="icon-box">
                                <img src="{{ asset('rosta/images/icon-why-choose-6.svg') }}" alt="Rosta service feature icon">
                            </div>
                            <div class="why-choose-item-content">
                                <h3>Hỗ trợ online mỗi ngày</h3>
                                <p>Bạn có thể liên hệ qua mạng xã hội hoặc điện thoại để đặt hàng và nhận tư vấn nhanh.</p>
                            </div>
                        </div>
                        <!-- Why Choose Item End -->
                    </div>
                    <!-- Why Choose List End -->
                </div>
            </div>
        </div>
    </div>
    <!-- Why Choose Us Section End -->

    <!-- Intro Video Section Start -->
    <div class="intro-video parallaxie" style="text-align: center;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 col-md-8">
                    <!-- Intro Video Content Start -->
                    <div class="intro-video-content">
                        <!-- Section Title Start -->
                        <div class="section-title">
                            <h3 class="wow fadeInUp">Hành Trình Phát Triển</h3>
                            <h2 class="text-anime-style-3" data-cursor="-opaque">Từ vườn đến bàn ăn với Cà phê robusta gia lai</h2>
                        </div>
                        <!-- Section Title End -->
                    </div>
                    <!-- Intro Video Content End -->
                </div>

                <div class="col-lg-6 col-md-4">
                    <!-- Intro Video Box Start -->
                    <div class="intro-video-box about-intro-video wow fadeInUp" data-wow-delay="0.2s">
                        <!-- Video Play Button Start -->
                        <div class="video-play-button">
                            <a href="https://www.youtube.com/watch?v=Y-x0efG1seA" class="popup-video" data-cursor-text="Play">
                                <i class="fa-solid fa-play"></i>
                            </a>
                            <p>xem video</p>
                        </div>
                        <!-- Video Play Button End -->
                    </div>
                    <!-- Intro Video Box End -->
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <!-- Intro Video Counters Start -->
                    <div class="intro-video-counters">
                        <!-- Video Counter Item Start -->
                        <div class="video-counter-item">
                            <div class="icon-box">
                                <img src="{{ asset('rosta/images/icon-intro-video-counter-1.svg') }}" alt="Restaurant statistic icon">
                            </div>
                            <div class="video-counter-content">
                                <h2><span class="counter">300</span>+</h2>
                            <p>khách thăm mỗi ngày</p>
                            </div>
                        </div>
                        <!-- Video Counter Item End -->

                        <!-- Video Counter Item Start -->
                        <div class="video-counter-item">
                            <div class="icon-box">
                                <img src="{{ asset('rosta/images/icon-intro-video-counter-2.svg') }}" alt="Restaurant statistic icon">
                            </div>
                            <div class="video-counter-content">
                                <h2><span class="counter">50</span></h2>
                            <p>công thức gợi ý</p>
                            </div>
                        </div>
                        <!-- Video Counter Item End -->

                        <!-- Video Counter Item Start -->
                        <div class="video-counter-item">
                            <div class="icon-box">
                                <img src="{{ asset('rosta/images/icon-intro-video-counter-3.svg') }}" alt="Restaurant statistic icon">
                            </div>
                            <div class="video-counter-content">
                                <h2><span class="counter">120</span>+</h2>
                                <p>đợt sự kiện đồng hành</p>
                            </div>
                        </div>
                        <!-- Video Counter Item End -->

                        <!-- Video Counter Item Start -->
                        <div class="video-counter-item">
                            <div class="icon-box">
                                <img src="{{ asset('rosta/images/icon-intro-video-counter-4.svg') }}" alt="Restaurant statistic icon">
                            </div>
                            <div class="video-counter-content">
                                <h2><span class="counter">500</span>+</h2>
                                <p>khách hàng hài lòng</p>
                            </div>
                        </div>
                        <!-- Video Counter Item End -->
                    </div>
                    <!-- Intro Video Counters End -->
                </div>
            </div>
        </div>
    </div>
    <!-- Intro Video Section End -->

    <!-- Our Pricing Section Start -->
    <div class="our-pricing">
        <div class="container">
            <div class="row section-row">
                <div class="col-lg-12">
                    <!-- Section Title Start -->
                    <div class="section-title">
                        <h3 class="wow fadeInUp">bảng giá nổi bật</h3>
                        <h2 class="text-anime-style-3" data-cursor="-opaque">Quality farm produce, fair value for every family</h2>
                    </div>
                    <!-- Section Title End -->
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="our-pricing-box tab-content" id="pricingtab">
                        <!-- Sidebar Our Support Nav start -->
                        <div class="our-support-nav wow fadeInUp" data-wow-delay="0.2s">
                            <ul class="nav nav-tabs" id="mvTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="btn-default btn-highlighted active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab" aria-selected="true">Cà phê</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="btn-default btn-highlighted" id="see-food-tab" data-bs-toggle="tab" data-bs-target="#see-food" type="button" role="tab" aria-selected="false">Maccamadia</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="btn-default btn-highlighted" id="desserts-tab" data-bs-toggle="tab" data-bs-target="#desserts" type="button" role="tab" aria-selected="false">Tiêu</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="btn-default btn-highlighted" id="drink-tab" data-bs-toggle="tab" data-bs-target="#drink" type="button" role="tab" aria-selected="false">Bơ</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="btn-default btn-highlighted" id="durian-tab" data-bs-toggle="tab" data-bs-target="#drink" type="button" role="tab" aria-selected="false">Sầu riêng</button>
                                </li>
                            </ul>
                        </div>
                        <!-- Sidebar Our Support Nav End -->

                        <!-- Pricing Boxes Start -->
                        <div class="pricing-boxes tab-pane fade show active" id="all" role="tabpanel">
                            <div class="row align-items-center">
                                <div class="col-lg-6">
                                    <!-- Pricing Image Start -->
                                    <div class="pricing-image">
                                        <figure class="image-anime">
                                            <video playsinline autoplay muted loop preload="none" poster="https://cdn.shopify.com/s/files/1/1707/3261/files/hp-poster.webp?v=1736778184">
                                                <source src="https://cdn.shopify.com/videos/c/o/v/f3a0b38123db492c8ddea379bb7e7474.mp4" type="video/mp4">
                                            </video>
                                        </figure>
                                    </div>
                                    <!-- Pricing Image End -->
                                </div>

                                <div class="col-lg-6">
                                    <!-- Our Menu List Start -->
                                    <div class="our-menu-list">
                                        <!-- Our Menu Item Start -->
                                        <div class="menu-list-item">
                                            <!-- Our Menu Image Start -->
                                            <div class="menu-list-image">
                                                <figure>
                                                    <img src="{{ asset('rosta/images/pricing-menu-1.png') }}" alt="Signature menu item 1">
                                                </figure>
                                            </div>
                                            <!-- Our Menu Image End -->
        
                                            <!-- Menu Item Body Start -->
                                            <div class="menu-item-body">
                                                <!-- Menu Item Title Start -->
                                                <div class="menu-item-title">
                                                    <h3>Cà phê robusta Gia lai</h3>
                                                    <hr>
                                                    <span>160.000đ</span>
                                                </div>
                                                <!-- Menu Item Title End -->
        
                                                <!-- Menu Item Content Start -->
                                                <div class="menu-item-content">
                                                    <p>Hương vị đậm, thơm rõ và hậu vị dễ chịu, phù hợp uống hằng ngày hoặc làm quà tặng đặc sản.</p>
                                                </div>
                                                <!-- Menu Item Content End -->
                                            </div>
                                            <!-- Menu Item Body End -->
                                        </div>
                                        
                                        <!-- Our Menu Item Start -->
                                        <div class="menu-list-item">
                                            <!-- Our Menu Image Start -->
                                            <div class="menu-list-image">
                                                <figure>
                                                    <img src="{{ asset('rosta/images/pricing-menu-2.png') }}" alt="Signature menu item 2">
                                                </figure>
                                            </div>
                                            <!-- Our Menu Image End -->
        
                                            <!-- Menu Item Body Start -->
                                            <div class="menu-item-body">
                                                <!-- Menu Item Title Start -->
                                                <div class="menu-item-title">
                                                    <h3>Hạt maccamadia</h3>
                                                    <hr>
                                                    <span>160.000đ</span>
                                                </div>
                                                <!-- Menu Item Title End -->
        
                                                <!-- Menu Item Content Start -->
                                                <div class="menu-item-content">
                                    <p>Hạt béo bùi, giàu dinh dưỡng, thích hợp dùng ăn nhẹ hoặc kết hợp cùng ngũ cốc và sữa chua.</p>
                                                </div>
                                                <!-- Our Menu Item End -->
                                                <!-- Menu Item Content End -->
                                            </div>
                                            <!-- Menu Item Body End -->
                                        </div>
                                        <!-- Our Menu Item End -->
                                        
                                        <!-- Our Menu Item Start -->
                                        <div class="menu-list-item">
                                            <!-- Our Menu Image Start -->
                                            <div class="menu-list-image">
                                                <figure>
                                                    <img src="{{ asset('rosta/images/pricing-menu-3.png') }}" alt="Signature menu item 3">
                                                </figure>
                                            </div>
                                            <!-- Our Menu Image End -->
        
                                            <!-- Menu Item Body Start -->
                                            <div class="menu-item-body">
                                                <!-- Menu Item Title Start -->
                                                <div class="menu-item-title">
                                                    <h3>Tiêu đen</h3>
                                                    <hr>
                                                    <span>160.000đ</span>
                                                </div>
                                                <!-- Menu Item Title End -->
        
                                                <!-- Menu Item Content Start -->
                                                <div class="menu-item-content">
                                                    <p>Tiêu đen hạt chắc, thơm nồng tự nhiên, giúp món ăn dậy mùi và đậm đà hơn.</p>
                                                </div>
                                                <!-- Menu Item Content End -->
                                            </div>
                                            <!-- Menu Item Body End -->
                                        </div>
                                        <!-- Our Menu Item End -->

                                        <!-- Our Menu Item Start -->
                                        <div class="menu-list-item">
                                            <!-- Our Menu Image Start -->
                                            <div class="menu-list-image">
                                                <figure>
                                                    <img src="{{ asset('rosta/images/pricing-menu-4.png') }}" alt="Signature menu item 4">
                                                </figure>
                                            </div>
                                            <!-- Our Menu Image End -->
        
                                            <!-- Menu Item Body Start -->
                                            <div class="menu-item-body">
                                                <!-- Menu Item Title Start -->
                                                <div class="menu-item-title">
                                                    <h3>Bơ sáp</h3>
                                                    <hr>
                                                    <span>160.000đ</span>
                                                </div>
                                                <!-- Menu Item Title End -->
        
                                                <!-- Menu Item Content Start -->
                                                <div class="menu-item-content">
                                                    <p>Bơ sáp dẻo mịn, vị béo ngậy tự nhiên, phù hợp làm sinh tố, ăn cùng bánh mì hoặc salad.</p>
                                                </div>
                                                <!-- Menu Item Content End -->
                                            </div>
                                            <!-- Menu Item Body End -->
                                        </div>
                                        <!-- Our Menu Item End -->

                                        <!-- Our Menu Item Start -->
                                        <div class="menu-list-item">
                                            <!-- Our Menu Image Start -->
                                            <div class="menu-list-image">
                                                <figure>
                                                    <img src="{{ asset('rosta/images/pricing-menu-2.png') }}" alt="Signature menu item 2">
                                                </figure>
                                            </div>
                                            <!-- Our Menu Image End -->
        
                                            <!-- Menu Item Body Start -->
                                            <div class="menu-item-body">
                                                <!-- Menu Item Title Start -->
                                                <div class="menu-item-title">
                                                    <h3>Sầu riêng</h3>
                                                    <hr>
                                                    <span>160.000đ</span>
                                                </div>
                                                <!-- Menu Item Title End -->
        
                                                <!-- Menu Item Content Start -->
                                                <div class="menu-item-content">
                                                    <p>Sầu riêng thơm đậm, múi vàng dẻo và ngọt béo, phù hợp cho khách yêu thích trái cây đặc sản.</p>
                                                </div>
                                                <!-- Menu Item Content End -->
                                            </div>
                                            <!-- Menu Item Body End -->
                                        </div>
                                        <!-- Our Menu Item End -->
                                    </div>
                                    <!-- Our Menu List End -->
                                </div>
                            </div>
                        </div>
                        <!-- Pricing Boxes End -->
                        
                        <!-- Pricing Boxes Start -->
                        <div class="pricing-boxes tab-pane fade" id="see-food" role="tabpanel">
                            <div class="row align-items-center">
                                <div class="col-lg-6">
                                    <!-- Pricing Image Start -->
                                    <div class="pricing-image">
                                        <figure class="image-anime">
                                            <img src="{{ asset('rosta/images/pricing-tab-image-2.jpg') }}" alt="Menu category image 2">
                                        </figure>
                                    </div>
                                    <!-- Pricing Image End -->
                                </div>

                                <div class="col-lg-6">
                                    <!-- Our Menu List Start -->
                                    <div class="our-menu-list">
                                        <!-- Our Menu Item Start -->
                                        <div class="menu-list-item">
                                            <!-- Our Menu Image Start -->
                                            <div class="menu-list-image">
                                                <figure>
                                                    <img src="{{ asset('rosta/images/pricing-menu-1.png') }}" alt="Signature menu item 1">
                                                </figure>
                                            </div>
                                            <!-- Our Menu Image End -->
        
                                            <!-- Menu Item Body Start -->
                                            <div class="menu-item-body">
                                                <!-- Menu Item Title Start -->
                                                <div class="menu-item-title">
                                                    <h3>Cà phê robusta hạt</h3>
                                                    <hr>
                                                    <span>$16.00</span>
                                                </div>
                                                <!-- Menu Item Title End -->
        
                                                <!-- Menu Item Content Start -->
                                                <div class="menu-item-content">
                                                    <p>Cà phê robusta gia lai vị đậm, hậu ngọt nhẹ, phù hợp pha phin và pha máy tại nhà.</p>
                                                </div>
                                                <!-- Menu Item Content End -->
                                            </div>
                                            <!-- Menu Item Body End -->
                                        </div>
                                        
                                        <!-- Our Menu Item Start -->
                                        <div class="menu-list-item">
                                            <!-- Our Menu Image Start -->
                                            <div class="menu-list-image">
                                                <figure>
                                                    <img src="{{ asset('rosta/images/pricing-menu-2.png') }}" alt="Signature menu item 2">
                                                </figure>
                                            </div>
                                            <!-- Our Menu Image End -->
        
                                            <!-- Menu Item Body Start -->
                                            <div class="menu-item-body">
                                                <!-- Menu Item Title Start -->
                                                <div class="menu-item-title">
                                                    <h3>Cà phê robusta xay</h3>
                                                    <hr>
                                                    <span>$16.00</span>
                                                </div>
                                                <!-- Menu Item Title End -->
        
                                                <!-- Menu Item Content Start -->
                                                <div class="menu-item-content">
                                                    <p>Bột xay mịn vừa, giữ mùi thơm tự nhiên, tiện lợi cho người bận rộn nhưng vẫn muốn uống cà phê ngon.</p>
                                                </div>
                                                <!-- Our Menu Item End -->
                                                <!-- Menu Item Content End -->
                                            </div>
                                            <!-- Menu Item Body End -->
                                        </div>
                                        <!-- Our Menu Item End -->
                                        
                                        <!-- Our Menu Item Start -->
                                        <div class="menu-list-item">
                                            <!-- Our Menu Image Start -->
                                            <div class="menu-list-image">
                                                <figure>
                                                    <img src="{{ asset('rosta/images/pricing-menu-3.png') }}" alt="Signature menu item 3">
                                                </figure>
                                            </div>
                                            <!-- Our Menu Image End -->
        
                                            <!-- Menu Item Body Start -->
                                            <div class="menu-item-body">
                                                <!-- Menu Item Title Start -->
                                                <div class="menu-item-title">
                                                    <h3>Hạt mắc ca sấy</h3>
                                                    <hr>
                                                    <span>$16.00</span>
                                                </div>
                                                <!-- Menu Item Title End -->
        
                                                <!-- Menu Item Content Start -->
                                                <div class="menu-item-content">
                                                    <p>Hạt mắc ca béo bùi, giàu dinh dưỡng, thích hợp ăn vặt lành mạnh cho cả nhà.</p>
                                                </div>
                                                <!-- Menu Item Content End -->
                                            </div>
                                            <!-- Menu Item Body End -->
                                        </div>
                                        <!-- Our Menu Item End -->

                                        <!-- Our Menu Item Start -->
                                        <div class="menu-list-item">
                                            <!-- Our Menu Image Start -->
                                            <div class="menu-list-image">
                                                <figure>
                                                    <img src="{{ asset('rosta/images/pricing-menu-4.png') }}" alt="Signature menu item 4">
                                                </figure>
                                            </div>
                                            <!-- Our Menu Image End -->
        
                                            <!-- Menu Item Body Start -->
                                            <div class="menu-item-body">
                                                <!-- Menu Item Title Start -->
                                                <div class="menu-item-title">
                                                    <h3>Tiêu đen hữu cơ</h3>
                                                    <hr>
                                                    <span>$16.00</span>
                                                </div>
                                                <!-- Menu Item Title End -->
        
                                                <!-- Menu Item Content Start -->
                                                <div class="menu-item-content">
                                                    <p>Tiêu đen hạt chắc, mùi thơm nồng tự nhiên, giúp món ăn đậm đà và tròn vị hơn.</p>
                                                </div>
                                                <!-- Menu Item Content End -->
                                            </div>
                                            <!-- Menu Item Body End -->
                                        </div>
                                        <!-- Our Menu Item End -->
                                    </div>
                                    <!-- Our Menu List End -->
                                </div>
                            </div>
                        </div>
                        <!-- Pricing Boxes End -->

                        <!-- Pricing Boxes Start -->
                        <div class="pricing-boxes tab-pane fade" id="desserts" role="tabpanel">
                            <div class="row align-items-center">
                                <div class="col-lg-6">
                                    <!-- Pricing Image Start -->
                                    <div class="pricing-image">
                                        <figure class="image-anime">
                                            <img src="{{ asset('rosta/images/pricing-tab-image-3.jpg') }}" alt="Menu category image 3">
                                        </figure>
                                    </div>
                                    <!-- Pricing Image End -->
                                </div>

                                <div class="col-lg-6">
                                    <!-- Our Menu List Start -->
                                    <div class="our-menu-list">
                                        <!-- Our Menu Item Start -->
                                        <div class="menu-list-item">
                                            <!-- Our Menu Image Start -->
                                            <div class="menu-list-image">
                                                <figure>
                                                    <img src="{{ asset('rosta/images/pricing-menu-1.png') }}" alt="Signature menu item 1">
                                                </figure>
                                            </div>
                                            <!-- Our Menu Image End -->
        
                                            <!-- Menu Item Body Start -->
                                            <div class="menu-item-body">
                                                <!-- Menu Item Title Start -->
                                                <div class="menu-item-title">
                                                    <h3>Cà phê robusta hạt</h3>
                                                    <hr>
                                                    <span>$16.00</span>
                                                </div>
                                                <!-- Menu Item Title End -->
        
                                                <!-- Menu Item Content Start -->
                                                <div class="menu-item-content">
                                                    <p>Cà phê robusta gia lai vị đậm, hậu ngọt nhẹ, phù hợp pha phin và pha máy tại nhà.</p>
                                                </div>
                                                <!-- Menu Item Content End -->
                                            </div>
                                            <!-- Menu Item Body End -->
                                        </div>
                                        
                                        <!-- Our Menu Item Start -->
                                        <div class="menu-list-item">
                                            <!-- Our Menu Image Start -->
                                            <div class="menu-list-image">
                                                <figure>
                                                    <img src="{{ asset('rosta/images/pricing-menu-2.png') }}" alt="Signature menu item 2">
                                                </figure>
                                            </div>
                                            <!-- Our Menu Image End -->
        
                                            <!-- Menu Item Body Start -->
                                            <div class="menu-item-body">
                                                <!-- Menu Item Title Start -->
                                                <div class="menu-item-title">
                                                    <h3>Cà phê robusta xay</h3>
                                                    <hr>
                                                    <span>$16.00</span>
                                                </div>
                                                <!-- Menu Item Title End -->
        
                                                <!-- Menu Item Content Start -->
                                                <div class="menu-item-content">
                                                    <p>Bột xay mịn vừa, giữ mùi thơm tự nhiên, tiện lợi cho người bận rộn nhưng vẫn muốn uống cà phê ngon.</p>
                                                </div>
                                                <!-- Our Menu Item End -->
                                                <!-- Menu Item Content End -->
                                            </div>
                                            <!-- Menu Item Body End -->
                                        </div>
                                        <!-- Our Menu Item End -->
                                        
                                        <!-- Our Menu Item Start -->
                                        <div class="menu-list-item">
                                            <!-- Our Menu Image Start -->
                                            <div class="menu-list-image">
                                                <figure>
                                                    <img src="{{ asset('rosta/images/pricing-menu-3.png') }}" alt="Signature menu item 3">
                                                </figure>
                                            </div>
                                            <!-- Our Menu Image End -->
        
                                            <!-- Menu Item Body Start -->
                                            <div class="menu-item-body">
                                                <!-- Menu Item Title Start -->
                                                <div class="menu-item-title">
                                                    <h3>Hạt mắc ca sấy</h3>
                                                    <hr>
                                                    <span>$16.00</span>
                                                </div>
                                                <!-- Menu Item Title End -->
        
                                                <!-- Menu Item Content Start -->
                                                <div class="menu-item-content">
                                                    <p>Hat mac ca beo bui, giau dinh duong, thich hop an vat lanh manh cho ca nha.</p>
                                                </div>
                                                <!-- Menu Item Content End -->
                                            </div>
                                            <!-- Menu Item Body End -->
                                        </div>
                                        <!-- Our Menu Item End -->

                                        <!-- Our Menu Item Start -->
                                        <div class="menu-list-item">
                                            <!-- Our Menu Image Start -->
                                            <div class="menu-list-image">
                                                <figure>
                                                    <img src="{{ asset('rosta/images/pricing-menu-4.png') }}" alt="Signature menu item 4">
                                                </figure>
                                            </div>
                                            <!-- Our Menu Image End -->
        
                                            <!-- Menu Item Body Start -->
                                            <div class="menu-item-body">
                                                <!-- Menu Item Title Start -->
                                                <div class="menu-item-title">
                                                    <h3>Tiêu đen hữu cơ</h3>
                                                    <hr>
                                                    <span>$16.00</span>
                                                </div>
                                                <!-- Menu Item Title End -->
        
                                                <!-- Menu Item Content Start -->
                                                <div class="menu-item-content">
                                                    <p>Tieu den hat chac, mui thom nong tu nhien, giup mon an dam da va tron vi hon.</p>
                                                </div>
                                                <!-- Menu Item Content End -->
                                            </div>
                                            <!-- Menu Item Body End -->
                                        </div>
                                        <!-- Our Menu Item End -->
                                    </div>
                                    <!-- Our Menu List End -->
                                </div>
                            </div>
                        </div>
                        <!-- Pricing Boxes End -->
                        
                        <!-- Pricing Boxes Start -->
                        <div class="pricing-boxes tab-pane fade" id="drink" role="tabpanel">
                            <div class="row align-items-center">
                                <div class="col-lg-6">
                                    <!-- Pricing Image Start -->
                                    <div class="pricing-image">
                                        <figure class="image-anime">
                                            <img src="{{ asset('rosta/images/pricing-tab-image-4.jpg') }}" alt="Menu category image 4">
                                        </figure>
                                    </div>
                                    <!-- Pricing Image End -->
                                </div>

                                <div class="col-lg-6">
                                    <!-- Our Menu List Start -->
                                    <div class="our-menu-list">
                                        <!-- Our Menu Item Start -->
                                        <div class="menu-list-item">
                                            <!-- Our Menu Image Start -->
                                            <div class="menu-list-image">
                                                <figure>
                                                    <img src="{{ asset('rosta/images/pricing-menu-1.png') }}" alt="Signature menu item 1">
                                                </figure>
                                            </div>
                                            <!-- Our Menu Image End -->
        
                                            <!-- Menu Item Body Start -->
                                            <div class="menu-item-body">
                                                <!-- Menu Item Title Start -->
                                                <div class="menu-item-title">
                                                    <h3>Cà phê robusta hạt</h3>
                                                    <hr>
                                                    <span>$16.00</span>
                                                </div>
                                                <!-- Menu Item Title End -->
        
                                                <!-- Menu Item Content Start -->
                                                <div class="menu-item-content">
                                                    <p>Cà phê robusta gia lai vị đậm, hậu ngọt nhẹ, phù hợp pha phin và pha máy tại nhà.</p>
                                                </div>
                                                <!-- Menu Item Content End -->
                                            </div>
                                            <!-- Menu Item Body End -->
                                        </div>
                                        
                                        <!-- Our Menu Item Start -->
                                        <div class="menu-list-item">
                                            <!-- Our Menu Image Start -->
                                            <div class="menu-list-image">
                                                <figure>
                                                    <img src="{{ asset('rosta/images/pricing-menu-2.png') }}" alt="Signature menu item 2">
                                                </figure>
                                            </div>
                                            <!-- Our Menu Image End -->
        
                                            <!-- Menu Item Body Start -->
                                            <div class="menu-item-body">
                                                <!-- Menu Item Title Start -->
                                                <div class="menu-item-title">
                                                    <h3>Cà phê robusta xay</h3>
                                                    <hr>
                                                    <span>$16.00</span>
                                                </div>
                                                <!-- Menu Item Title End -->
        
                                                <!-- Menu Item Content Start -->
                                                <div class="menu-item-content">
                                                    <p>Bột xay mịn vừa, giữ mùi thơm tự nhiên, tiện lợi cho người bận rộn nhưng vẫn muốn uống cà phê ngon.</p>
                                                </div>
                                                <!-- Our Menu Item End -->
                                                <!-- Menu Item Content End -->
                                            </div>
                                            <!-- Menu Item Body End -->
                                        </div>
                                        <!-- Our Menu Item End -->
                                        
                                        <!-- Our Menu Item Start -->
                                        <div class="menu-list-item">
                                            <!-- Our Menu Image Start -->
                                            <div class="menu-list-image">
                                                <figure>
                                                    <img src="{{ asset('rosta/images/pricing-menu-3.png') }}" alt="Signature menu item 3">
                                                </figure>
                                            </div>
                                            <!-- Our Menu Image End -->
        
                                            <!-- Menu Item Body Start -->
                                            <div class="menu-item-body">
                                                <!-- Menu Item Title Start -->
                                                <div class="menu-item-title">
                                                    <h3>Hạt mắc ca sấy</h3>
                                                    <hr>
                                                    <span>$16.00</span>
                                                </div>
                                                <!-- Menu Item Title End -->
        
                                                <!-- Menu Item Content Start -->
                                                <div class="menu-item-content">
                                                    <p>Hat mac ca beo bui, giau dinh duong, thich hop an vat lanh manh cho ca nha.</p>
                                                </div>
                                                <!-- Menu Item Content End -->
                                            </div>
                                            <!-- Menu Item Body End -->
                                        </div>
                                        <!-- Our Menu Item End -->

                                        <!-- Our Menu Item Start -->
                                        <div class="menu-list-item">
                                            <!-- Our Menu Image Start -->
                                            <div class="menu-list-image">
                                                <figure>
                                                    <img src="{{ asset('rosta/images/pricing-menu-4.png') }}" alt="Signature menu item 4">
                                                </figure>
                                            </div>
                                            <!-- Our Menu Image End -->
        
                                            <!-- Menu Item Body Start -->
                                            <div class="menu-item-body">
                                                <!-- Menu Item Title Start -->
                                                <div class="menu-item-title">
                                                    <h3>Tiêu đen hữu cơ</h3>
                                                    <hr>
                                                    <span>$16.00</span>
                                                </div>
                                                <!-- Menu Item Title End -->
        
                                                <!-- Menu Item Content Start -->
                                                <div class="menu-item-content">
                                                    <p>Tieu den hat chac, mui thom nong tu nhien, giup mon an dam da va tron vi hon.</p>
                                                </div>
                                                <!-- Menu Item Content End -->
                                            </div>
                                            <!-- Menu Item Body End -->
                                        </div>
                                        <!-- Our Menu Item End -->
                                    </div>
                                    <!-- Our Menu List End -->
                                </div>
                            </div>
                        </div>
                        <!-- Pricing Boxes End -->

                        <div class="section-footer-text wow fadeInUp" data-wow-delay="0.2s">
                            <p>Bạn đang tìm nông sản sạch? <a href="{{ route('book-table') }}">Liên hệ đặt hàng ngay!</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Our Pricing Section End -->

    <!-- Interactive Process Layout Start -->
    <div class="interactive interactive-process-layout">
        <!-- Interactive Process Wrapper Start -->
        <div class="interactive-interactive-process-wrapper interactive-wrapper">
            <div class="interactive-con">
                <!-- Interactive Inner Grid Start -->
                <div watch-visibility="" class="arches scheme-light visible" style="background-color:#191919">
                    <div class="container">
                        <div class="text">
                            <div class="inner">
                                <h2 class="blinds-text" aria-label="Sản phẩm">
                                    <div class="blinds-text-wrapper">
                                        <div class="blinds-word" aria-hidden="true">
                                            <span class="char-clip"><span style="--i:0ms">S</span></span>
                                            <span class="char-clip"><span style="--i:20ms">a</span></span>
                                            <span class="char-clip"><span style="--i:40ms">n</span></span>
                                            <span class="char-clip"><span style="--i:60ms">&nbsp;</span></span>
                                        </div>
                                        <div class="blinds-word" aria-hidden="true">
                                            <span class="char-clip"><span style="--i:0ms">p</span></span>
                                            <span class="char-clip"><span style="--i:20ms">h</span></span>
                                            <span class="char-clip"><span style="--i:40ms">a</span></span>
                                            <span class="char-clip"><span style="--i:60ms">m</span></span>
                                        </div>
                                    </div>
                                </h2>
                                <div data-reset="true" data-delay="200" reveal="" data-sr-id="7" style="visibility: visible; opacity: 1; transform: matrix3d(1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1); transition: all, opacity 0.6s ease-out 0.2s, transform 0.6s ease-out 0.2s;">
                                    <p>Khám phá bộ sưu tập nông sản mới từ vùng đất Gia Lai và Tây Nguyên. Cập nhật sản phẩm mới mỗi tuần qua bản tin của Tiệm Nhà Duy.</p>
                                </div>
                                <a data-reset="true" data-delay="400" reveal="" class="button" href="{{ route('services') }}" data-sr-id="8" style="visibility: visible; opacity: 1; transform: matrix3d(1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1); transition: all, opacity 0.6s ease-out 0.4s, transform 0.6s ease-out 0.4s;">Xem danh mục sản phẩm</a>
                            </div>
                        </div>
                    </div>
                    
                </div>
                <!-- Interactive Inner Grid End -->

                <!-- Interactive Process Image Start -->
                <div class="interactive-process-list-image video-split">
                    <div class="interactive-process-image img-0 show">
                        <video autoplay loop muted playsinline preload="none" poster="https://product.onyxcontent.com/media/pages/ecom/home/487e8943b1-1749499406/cover-new.webp">
                            <source type="video/mp4" src="https://product.onyxcontent.com/media/pages/ecom/home/224fcadae6-1749498876/new.mp4">
                        </video>
                        <div class="interactive-process-caption">
                            <h3>Chư Sê - Gia Lai</h3>
                            <p>Vùng đất nổi tiếng với cà phê robusta hạt chắc, hương đậm và hậu vị rõ nét, rất được yêu thích tại Việt Nam.</p>
                            <a href="{{ route('services') }}">Xem cà phê robusta →</a>
                        </div>
                    </div>
                    <div class="interactive-process-image img-1">
                        <video autoplay loop muted playsinline preload="none" poster="https://product.onyxcontent.com/media/pages/ecom/home/f3c96161a3-1774303240/honduras-cover.webp">
                            <source type="video/mp4" src="https://product.onyxcontent.com/media/pages/ecom/home/4108535e73-1774303284/honduras-fill.mp4">
                        </video>
                        <div class="interactive-process-caption">
                            <h3>Mắc ca Tây Nguyên</h3>
                            <p>Hạt mắc ca được chọn lọc, béo bùi tự nhiên, thích hợp ăn trực tiếp hoặc kết hợp trong các khẩu phần dinh dưỡng.</p>
                            <a href="{{ route('services') }}">Xem sản phẩm mắc ca →</a>
                        </div>
                    </div>
                    <div class="interactive-process-image img-2">
                        <video autoplay loop muted playsinline preload="none" poster="https://product.onyxcontent.com/media/pages/ecom/home/851e06e9d6-1763745870/colombia-cover-2.webp">
                            <source type="video/mp4" src="https://product.onyxcontent.com/media/pages/ecom/home/cb45055b2c-1763745691/colombia-10.mp4">
                        </video>
                        <div class="interactive-process-caption">
                            <h3>Tiêu và Bơ</h3>
                            <p>Tiêu đen hữu cơ cay thơm tự nhiên và bơ sáp dẻo ngon là bộ đôi nông sản được khách hàng đặt mua nhiều.</p>
                            <a href="{{ route('services') }}">Xem tiêu và bơ →</a>
                        </div>
                    </div>
                    <div class="interactive-process-image img-3">
                        <video autoplay loop muted playsinline preload="none" poster="https://product.onyxcontent.com/media/pages/ecom/home/487e8943b1-1749499406/cover-new.webp">
                            <source type="video/mp4" src="https://product.onyxcontent.com/media/pages/ecom/home/224fcadae6-1749498876/new.mp4">
                        </video>
                    </div>
                </div>
                <!-- Interactive Process Image End -->
            </div>
        </div>
        <!-- Interactive Process Wrapper End -->
    </div>
    <!-- Interactive Process Layout End -->

    <!-- Our Offers Section Start -->
    <div class="our-offers">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-5">
                    <!-- Our Offers Content Start -->
                    <div class="our-offers-content">
                        <!-- Section Title Start -->
                        <div class="section-title">
                            <h3 class="wow fadeInUp">chúng tôi cung cấp gì?</h3>
                            <h2 class="text-anime-style-3" data-cursor="-opaque">Sản phẩm cho nhu cầu thưởng thức, quà tặng và lưu niệm</h2>
                            <p class="wow fadeInUp" data-wow-delay="0.2s">Chúng tôi cung cấp cà phê, mắc ca, tiêu và bơ phù hợp để dùng hằng ngày, làm quà tặng ý nghĩa hoặc chọn làm sản phẩm lưu niệm đặc trưng.</p>
                        </div>
                        <!-- Section Title End -->

                        <!-- Offer Accordian Start -->
                        <div class="offers-accordion" id="offer-accordion">
                            <!-- Offer Accordian Item Start -->
                            <div class="accordion-item wow fadeInUp" data-wow-delay="0.4s">
                                <h2 class="accordion-header" id="offersheading1">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#offerscollapse1" aria-expanded="true" aria-controls="offerscollapse1">
                                        Combo gia đình
                                    </button>
                                </h2>
                                <div id="offerscollapse1" class="accordion-collapse collapse show" aria-labelledby="offersheading1" data-bs-parent="#offer-accordion">
                                    <div class="accordion-body">
                                        <p>Gói sản phẩm tổng hợp cà phê robusta, mắc ca, tiêu và bơ phù hợp sử dụng hằng ngày.</p>
                                    </div>
                                </div>
                            </div>
                            <!-- Offer Accordian Item End -->
                            
                            <!-- Offer Accordian Item Start -->
                            <div class="accordion-item wow fadeInUp" data-wow-delay="0.6s">
                                <h2 class="accordion-header" id="offersheading2">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#offerscollapse2" aria-expanded="true" aria-controls="offerscollapse2">
                                        Combo quà tặng
                                    </button>
                                </h2>
                                <div id="offerscollapse2" class="accordion-collapse collapse" aria-labelledby="offersheading2" data-bs-parent="#offer-accordion">
                                    <div class="accordion-body">
                                        <p>Đóng gói lịch sự, phù hợp làm quà cho đối tác, người thân trong các dịp quan trọng.</p>
                                    </div>
                                </div>
                            </div>
                            <!-- Offer Accordian Item End -->
                            
                            <!-- Offer Accordian Item Start -->
                            <div class="accordion-item wow fadeInUp" data-wow-delay="0.8s">
                                <h2 class="accordion-header" id="offersheading3">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#offerscollapse3" aria-expanded="true" aria-controls="offerscollapse3">
                                        Đặt hàng định kỳ
                                    </button>
                                </h2>
                                <div id="offerscollapse3" class="accordion-collapse collapse" aria-labelledby="offersheading3" data-bs-parent="#offer-accordion">
                                    <div class="accordion-body">
                                        <p>Hỗ trợ đơn hàng theo tuần/tháng để bạn luôn có nông sản tươi mới, ổn định chất lượng.</p>
                                    </div>
                                </div>
                            </div>
                            <!-- Offer Accordian Item End -->
                        </div>
                        <!-- Offer Accordian End -->

                        <!-- Offer Button Start -->
                        <div class="offer-button wow fadeInUp" data-wow-delay="1s">
                            <a href="{{ route('book-table') }}" class="btn-default">Đặt lịch tư vấn ngay</a>
                        </div>
                        <!-- Offer Button End -->
                    </div>
                    <!-- Our Offers Content End -->
                </div>
                
                <div class="col-lg-7">
                    <!-- Our Offer Images Start -->
                    <div class="our-offers-images">
                        <!-- Offer Image Start -->
                        <div class="offer-image">
                            <figure class="image-anime">
                                <img src="{{ asset('rosta/images/offer-image.jpg') }}" alt="Special offer at Rosta">
                            </figure>
                        </div>
                        <!-- Offer Image End -->
                        
                        <!-- Offer Circle Image 1 Start -->
                        <div class="offer-circle-image-1">
                            <figure class="image-anime">
                                <img src="{{ asset('rosta/images/offer-circle-image-1.jpg') }}" alt="Special offer detail image">
                            </figure>
                        </div>  
                        <!-- Offer Circle Image 1 End -->
                        
                        <!-- Offer Circle Image 2 Start -->
                        <div class="offer-circle-image-2">
                            <figure class="image-anime">
                                <img src="{{ asset('rosta/images/offer-circle-image-2.jpg') }}" alt="Special offer detail image">
                            </figure>
                        </div>
                        <!-- Offer Circle Image 2 End -->
                    </div>
                    <!-- Our Offer Images End -->
                </div>
            </div>
        </div>
    </div>
    <!-- Our Offers Section End -->

    <div watch-visibility="" class="doubleSplit visible">
        <div class="container">
            <div class="a-split">
                <div class="background">
                    <div class="blur"></div>
                    <video autoplay preload="none" id="video_block_9c49ebdd-6f92-4425-88c1-54a3cf5cf4d9" play-on-visible="" loop="" muted="" playsinline="" poster="https://product.onyxcontent.com/media/pages/ecom/home/e70fe566a3-1737134861/subscription-cover.webp" class="visible">
                        <source type="video/mp4" src="https://product.onyxcontent.com/media/pages/ecom/home/a247c39541-1736883966/subscribe.mp4">
                    </video>
                </div>
                <div class="text">
                    <h2>Khám phá</h2>
                    <p>Mỗi tuần chúng tôi cập nhật cà phê robusta chất lượng cao từ Chư Sê Gia Lai, giúp bạn bắt đầu ngày mới đầy năng lượng.</p>
                    <a href="{{ route('services') }}">Xem bộ sưu tập nông sản</a>
                </div>
            </div>

            <div class="a-split">
                <div class="background">
                    <div class="blur"></div>
                    <video autoplay preload="none" id="video_block_9c49ebdd-6f92-4425-88c1-54a3cf5cf4d9" play-on-visible="" loop="" muted="" playsinline="" poster="https://product.onyxcontent.com/media/pages/ecom/home/d800b359f8-1736877618/wholesale-cover-image.webp" class="visible">
                        <source type="video/mp4" src="https://product.onyxcontent.com/media/pages/ecom/home/b823d0ebf9-1736877524/wholesale-video.mp4">
                    </video>
                </div>
                <div class="text">
                    <h2>phát huy</h2>
                    <p>Nếu bạn cần nguồn hàng ổn định cho quán cà phê, cửa hàng nông sản hoặc kênh phân phối, chúng tôi sẵn sàng đồng hành với chính sách linh hoạt.</p>
                    <a href="{{ route('about') }}">Tìm hiểu thêm về Tiệm Nhà Duy</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Our Testimonials Section Start -->
    <div class="our-testimonials parallaxie">
        <div class="container">
            <div class="row section-row">
                <div class="col-lg-12">
                    <!-- Section Title Start -->
                    <div class="section-title">
                        <h3 class="wow fadeInUp">cảm nhận khách hàng</h3>
                        <h2 class="text-anime-style-3" data-cursor="-opaque">Người đã dùng sẽ kể cho bạn nghe</h2>
                    </div>
                    <!-- Section Title End -->
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <!-- Testimonial Slider Start -->
                    <div class="testimonial-slider">
                        <div class="swiper">
                            <div class="swiper-wrapper" data-cursor-text="Drag">
                                <!-- Testimonial Slide Start -->
                                <div class="swiper-slide">
                                    <div class="testimonial-item">
                                        <div class="testimonial-content">
                                            <p>“ Tôi rất an tâm khi đặt mua Cà phê robusta gia lai tại Tiệm Nhà Duy. Hương vị đậm, giao hàng nhanh và chất lượng đúng như giới thiệu. </p>
                                        </div>
                                        <div class="author-info">
                                            <p>Chị Lan, chủ quán cà phê tại Pleiku</p>
                                        </div>                                    
                                    </div>
                                </div>
                                <!-- Testimonial Slide End -->
                    
                                <!-- Testimonial Slide Start -->
                                <div class="swiper-slide">
                                    <div class="testimonial-item">
                                        <div class="testimonial-content">
                                            <p>“ Gia đình tôi đặt mắc ca, tiêu đen và bơ thường xuyên. Sản phẩm tươi mới, đóng gói đẹp, rất phù hợp để dùng và tặng người thân. </p>
                                        </div>
                                        <div class="author-info">
                                            <p>Anh Hiếu, khách hàng thân thiết</p>
                                        </div>                                    
                                    </div>
                                </div>
                                <!-- Testimonial Slide End -->                              
                            </div>
                            <div class="testimonial-btn">
                                <div class="testimonial-btn-prev"></div>
                                <div class="testimonial-btn-next"></div>
                            </div>
                        </div>
                    </div>
                    <!-- Testimonial Slider End -->
                </div>
            </div>
        </div>
    </div>
    <!-- Our Testimonials Section End -->

    <!-- CTA Box Section Start -->
    <div watch-visibility="" class="textSplit scheme- align-right visible" style="background-color:#eee9df">
        <div class="container">
            <div class="text">
                <div class="inner">
                    <h2 class="blinds-text">cafe<br>expressions</h2>
                    <div>
                        <p>Trong suốt năm năm qua, chúng tôi đã phát triển dòng thức uống hoàn thiện được tuyển chọn kỹ lưỡng, tạo nên với cùng sự chỉn chu, chính xác và tiêu chuẩn đã làm nên tên tuổi quán cà phê đạt nhiều giải thưởng của Onyx.</p>
                        <p>Tìm hiểu thêm về chương trình mới bằng cách nhấp vào liên kết bên dưới.</p>
                    </div>
                    <a class="button" href="https://onyxcoffeelab.com/products/cafe-expressions">Tìm hiểu thêm về Cafe Expressions</a>
                </div>
            </div>
            <div class="media">
                <video preload="none" id="video_block_2ad2bed3-6bd5-4aee-a1e3-589e3fd4b7ff" autoplay loop muted playsinline poster="https://product.onyxcontent.com/media/pages/ecom/home/6dffbb1657-1776363338/expressions-cover.webp" class="visible">
                    <source type="video/mp4" src="https://product.onyxcontent.com/media/pages/ecom/home/42525f65da-1776363220/insta_square.mp4">
                </video>
            </div>
        </div>
    </div>
    <!-- CTA Box Section End -->
@endsection
