@props(['cartCount' => 0, 'user' => null])

<!-- Header Area -->
<div class="header-area" id="headerArea">
    <div class="container h-100 d-flex align-items-center justify-content-between d-flex rtl-flex-d-row-r">
        <!-- Logo Wrapper -->
        <div class="logo-wrapper">
            <a href="{{ route('home') }}">
                <img src="{{ asset('frontend/img/core-img/logo-small.png') }}" alt="{{ config('app.name') }} Logo">
            </a>
        </div>
        
        <div class="navbar-logo-container d-flex align-items-center">
            <!-- Cart Icon -->
            <div class="cart-icon-wrap">
                <a href="{{ route('cart.index') }}">
                    <i class="ti ti-basket-bolt"></i>
                    <span>{{ $cartCount }}</span>
                </a>
            </div>
            
            <!-- User Profile Icon -->
            <div class="user-profile-icon ms-2">
                @auth
                    <a href="{{ route('profile.show') }}">
                        <img src="{{ $user?->avatar ?? asset('frontend/img/bg-img/9.jpg') }}" 
                             alt="{{ $user?->name ?? 'User' }}">
                    </a>
                @else
                    <a href="{{ route('login') }}">
                        <i class="ti ti-user"></i>
                    </a>
                @endauth
            </div>
            
            <!-- Navbar Toggler -->
            <div class="suha-navbar-toggler ms-2" data-bs-toggle="offcanvas" data-bs-target="#suhaOffcanvas" aria-controls="suhaOffcanvas">
                <div><span></span><span></span><span></span></div>
            </div>
        </div>
    </div>
</div>

