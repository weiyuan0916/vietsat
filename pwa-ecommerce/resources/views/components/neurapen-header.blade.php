<!-- Tiệm Nhà Duy Header -->
<header class="header-neurapen">
    <div class="container-custom">
        <div class="d-flex align-items-center justify-content-between">
            <!-- Logo -->
            <div class="logo-wrapper">
                <a href="{{ route('home') }}" class="text-decoration-none">
                    <h1 class="gradient-text mb-0" style="font-size: 2rem; font-weight: 600; letter-spacing: -0.05em;">
                        Tiệm Nhà Duy
                    </h1>
                </a>
            </div>
            
            <!-- Navigation -->
            <nav class="d-none d-md-flex align-items-center gap-4">
                <a href="{{ route('home') }}" class="text-decoration-none text-white">Trang chủ</a>
                <a href="#services" class="text-decoration-none text-white">Dịch vụ</a>
                <a href="#features" class="text-decoration-none text-white">Tính năng</a>
                <a href="#faq" class="text-decoration-none text-white">FAQ</a>
                <a href="#contact" class="text-decoration-none text-white">Liên hệ</a>
            </nav>
            
            <!-- CTA Buttons -->
            <div class="d-flex align-items-center gap-3">
                @auth
                    <a href="{{ route('profile.show') }}" class="text-decoration-none text-white">
                        <i class="ti ti-user"></i>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="text-decoration-none text-white">Đăng nhập</a>
                @endauth
                <a href="#contact" class="btn-gradient">Liên hệ ngay</a>
                
                <!-- Mobile Menu Toggle -->
                <button class="d-md-none btn btn-link text-white" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu">
                    <i class="ti ti-menu-2"></i>
                </button>
            </div>
        </div>
    </div>
</header>

<!-- Mobile Menu -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="mobileMenu">
    <div class="offcanvas-header" style="background-color: var(--color-dark-bg-secondary);">
        <h5 class="offcanvas-title gradient-text">Tiệm Nhà Duy</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body" style="background-color: var(--color-dark-bg);">
        <nav class="d-flex flex-column gap-3">
            <a href="{{ route('home') }}" class="text-decoration-none text-white">Trang chủ</a>
            <a href="#services" class="text-decoration-none text-white">Dịch vụ</a>
            <a href="#features" class="text-decoration-none text-white">Tính năng</a>
            <a href="#faq" class="text-decoration-none text-white">FAQ</a>
            <a href="#contact" class="text-decoration-none text-white">Liên hệ</a>
            @guest
                <a href="{{ route('login') }}" class="text-decoration-none text-white">Đăng nhập</a>
            @endguest
        </nav>
    </div>
</div>

