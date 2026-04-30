    <!-- Topbar Section Start -->
    <div class="topbar">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-9">
                    <!-- Topbar Contact Information Start -->
                    <div class="topbar-contact-info">
                        <ul>
                            <li><a href="mailto:support@tiemnhaduy.com"><img src="{{ asset('rosta/images/icon-mail.svg') }}" alt="Email icon">support@tiemnhaduy.com</a></li>
                            <li><img src="{{ asset('rosta/images/icon-location.svg') }}" alt="Location icon">Xã Chư Sê, Tỉnh Gia Lai, Vietnam</li>
                        </ul>
                    </div>
                    <!-- Topbar Contact Information End -->
                </div>

                <div class="col-md-3">
                    <!-- Topbar Social Links Start -->
                    <div class="topbar-social-links">
                        <ul>
                            <li><a href="https://www.instagram.com/tiemnhaduy"><i class="fa-brands fa-instagram"></i></a></li>
                            <li><a href="https://www.facebook.com/tiemnhaduy"><i class="fa-brands fa-facebook-f"></i></a></li>
                            <li><a href="https://maps.app.goo.gl/JXTdYnauTKkTRdbi7"><i class="fa-solid fa-location-dot"></i></a></li>
                        </ul>
                    </div>
                    <!-- Topbar Social Links End -->
                </div>
            </div>
        </div>
    </div>
    <!-- Topbar Section End -->

    <!-- Header Start -->
	<header class="main-header">
		<div class="header-sticky">
			<nav class="navbar navbar-expand-lg">
				<div class="container">
					<!-- Logo Start -->
					<a class="navbar-brand" href="{{ route('home') }}">
						<img src="{{ asset('rosta/images/tiemnhaduy.svg') }}" alt="Rosta Coffee logo">
					</a>
					<!-- Logo End -->

					<!-- Main Menu Start -->
					<div class="collapse navbar-collapse main-menu">
                        <div class="nav-menu-wrapper">
                            <ul class="navbar-nav mr-auto" id="menu">
                                <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">Trang chủ</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{ route('about') }}">Về chúng tôi</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{ route('services') }}">Dịch vụ</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{ route('services') }}">Sản phẩm</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{ route('contact') }}">Liên hệ</a></li>                             
                                <li class="nav-item highlighted-menu"><a class="nav-link" href="{{ route('book-table') }}">Đặt lịch tư vấn</a></li>                             
                            </ul>
                        </div>

                        <!-- Header Button Box Start -->
                        <div class="header-button-box">
                            <!-- Header Btn Start -->
                            <div class="header-btn">
                               <a href="{{ route('book-table') }}" class="btn-default btn-highlighted">Đặt lịch ngay</a>
                            </div>
                            <!-- Header Btn End -->

                            <!-- Header Sidebar Btn Start -->
                            <div class="header-sidebar-btn">
                                <!-- Toggle Button trigger modal Start -->
                                <button class="btn btn-popup" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight"><img src="{{ asset('rosta/images/header-sidebar-btn.svg') }}" alt="Menu toggle icon"></button>
                                <!-- Toggle Button trigger modal End -->

                                <!-- Header Sidebar Start -->
                                <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight">
                                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                                    
                                    <!-- Offcanvas Body Start -->
                                    <div class="offcanvas-body">
                                        <!-- Header Title Box Start -->
                                        <div class="header-title-box">
                                            <h2>Welcome to Tiem Nha Duy</h2>
                                            <p>Chuyên cung cấp nông sản chất lượng: Cà phê robusta gia lai, mắc ca, tiêu và bơ sạch.</p>
                                        </div>
                                        <!-- Header Title Box End -->

                                        <!-- Header Sidebar Info Start -->
                                        <div class="header-sidebar-info">
                                            <h2><a href="tel:+84981314516">+84981314516</a></h2>
                                            <ul>
                                                <li>Xã Chư Sê, Tỉnh Gia Lai, Vietnam</li>
                                                <li><a href="mailto:support@tiemnhaduy.com">support@tiemnhaduy.com</a></li>
                                            </ul>
                                        </div>
                                        <!-- Header Sidebar Info End -->

                                        <!-- Header Sidebar Timing Start -->
                                        <div class="header-sidebar-timing">
                                            <ul>
                                                <li>Thứ Hai - Thứ Sáu : 08:00 - 21:00</li>
                                                <li>Thứ Bảy - Chủ Nhật : 09:00 - 22:00</li>
                                                <li>Ngày lễ : Vui lòng liên hệ trước</li>
                                            </ul>
                                        </div>
                                        <!-- Header Sidebar Timing End -->
                                        
                                        <div class="onyx-overlay-links">
                                            <a class="primary" href="{{ route('home') }}">Trang chủ</a>
                                            <a class="primary" href="{{ route('about') }}">Về chúng tôi</a>
                                            <a class="primary" href="{{ route('services') }}">Dịch vụ</a>
                                            <a class="primary" href="{{ route('services') }}">Sản phẩm</a>
                                            <a class="primary" href="{{ route('contact') }}">Liên hệ</a>
                                            <a class="primary" href="{{ route('book-table') }}">Đặt lịch tư vấn</a>
                                        </div>
                                        <!-- Header Sidebar Social List Start -->
                                        <div class="header-sidebar-social-list">
                                            <ul>
                                                <li><a href="https://www.instagram.com/tiemnhaduy"><i class="fa-brands fa-instagram"></i></a></li>
                                                <li><a href="https://www.facebook.com/tiemnhaduy"><i class="fa-brands fa-facebook-f"></i></a></li>
                                                <li><a href="https://maps.app.goo.gl/JXTdYnauTKkTRdbi7"><i class="fa-solid fa-location-dot"></i></a></li>
                                            </ul>
                                        </div>
                                        <!-- Header Sidebar Social List End -->
                                    </div>
                                    <!-- Offcanvas Body End -->
                                </div>
                                <!-- Header Sidebar End -->
                            </div>
                            <!-- Header Sidebar Btn End -->
                        </div>     
                        <!-- Header Button Box End -->                   
					</div>
					<!-- Main Menu End -->
					<div class="navbar-toggle"></div>
				</div>
			</nav>
			<div class="responsive-menu"></div>
		</div>
	</header>
	<!-- Header End -->