{{-- Localized Header Component - Example of how to use localization --}}
<header class="header-area" id="headerArea">
    <div class="container h-100 d-flex align-items-center justify-content-between d-flex rtl-flex-d-row-r">
        {{-- Logo Wrapper --}}
        <div class="logo-wrapper">
            <a href="{{ route('home') }}">
                <img src="{{ asset('img/core-img/logo-small.png') }}" alt="{{ $texts['footer']['brand_name'] ?? 'Logo' }}">
            </a>
        </div>
        <div class="navbar-logo-container d-flex align-items-center">
            {{-- Cart Icon --}}
            <div class="cart-icon-wrap">
                <a href="{{ route('cart.index') }}">
                    <i class="ti ti-basket-bolt"></i>
                    <span>13</span>
                </a>
            </div>
            {{-- User Profile Icon --}}
            <div class="user-profile-icon ms-2">
                <a href="{{ route('profile.show') }}">
                    <img src="{{ asset('img/bg-img/9.jpg') }}" alt="{{ $texts['menu']['my_profile'] ?? 'Profile' }}">
                </a>
            </div>
            {{-- Navbar Toggler --}}
            <div class="suha-navbar-toggler ms-2" data-bs-toggle="offcanvas" data-bs-target="#suhaOffcanvas" aria-controls="suhaOffcanvas">
                <div><span></span><span></span><span></span></div>
            </div>
        </div>
    </div>
</header>

{{-- Offcanvas Menu --}}
<div class="offcanvas offcanvas-start suha-offcanvas-wrap" tabindex="-1" id="suhaOffcanvas" aria-labelledby="suhaOffcanvasLabel">
    {{-- Close button --}}
    <button class="btn-close btn-close-white" type="button" data-bs-dismiss="offcanvas" aria-label="{{ $texts['common']['close'] ?? 'Close' }}"></button>

    {{-- Offcanvas body --}}
    <div class="offcanvas-body">
        {{-- Sidenav Profile --}}
        <div class="sidenav-profile">
            <div class="user-profile">
                <img src="{{ asset('img/bg-img/9.jpg') }}" alt="{{ $texts['menu']['my_profile'] ?? 'Profile' }}">
            </div>
            <div class="user-info">
                <h5 class="user-name mb-1 text-white">{{ $texts['menu']['my_profile'] ?? 'Suha Sarah' }}</h5>
                <p class="available-balance text-white">{{ $texts['menu']['current_balance'] ?? 'Current Balance' }} $<span class="counter">99</span></p>
            </div>
        </div>

        {{-- Sidenav Nav --}}
        <ul class="sidenav-nav ps-0">
            <li>
                <a href="{{ route('profile.show') }}">
                    <i class="ti ti-user"></i>{{ $texts['menu']['my_profile'] ?? 'My Profile' }}
                </a>
            </li>
            <li>
                <a href="{{ route('notifications.index') }}">
                    <i class="ti ti-bell-ringing lni-tada-effect"></i>{{ $texts['menu']['notifications'] ?? 'Notifications' }}
                    <span class="ms-1 badge badge-warning">3</span>
                </a>
            </li>
            <li class="suha-dropdown-menu">
                <a href="#">{{ $texts['menu']['shop_pages'] ?? 'Shop Pages' }}</a>
                <ul>
                    <li><a href="{{ route('shop.grid') }}">- {{ $texts['menu']['shop_grid'] ?? 'Shop Grid' }}</a></li>
                    <li><a href="{{ route('shop.list') }}">- {{ $texts['menu']['shop_list'] ?? 'Shop List' }}</a></li>
                    <li><a href="{{ route('products.show', 1) }}">- {{ $texts['menu']['product_details'] ?? 'Product Details' }}</a></li>
                    <li><a href="{{ route('products.featured') }}">- {{ $texts['menu']['featured_products'] ?? 'Featured Products' }}</a></li>
                    <li><a href="{{ route('products.flash-sale') }}">- {{ $texts['menu']['flash_sale'] ?? 'Flash Sale' }}</a></li>
                </ul>
            </li>
            <li>
                <a href="{{ route('pages.index') }}">
                    <i class="ti ti-notebook"></i>{{ $texts['menu']['all_pages'] ?? 'All Pages' }}
                </a>
            </li>
            <li class="suha-dropdown-menu">
                <a href="{{ route('wishlist.index') }}">
                    <i class="ti ti-heart"></i>{{ $texts['menu']['my_wishlist'] ?? 'My Wishlist' }}
                </a>
                <ul>
                    <li><a href="{{ route('wishlist.grid') }}">- {{ $texts['menu']['wishlist_grid'] ?? 'Wishlist Grid' }}</a></li>
                    <li><a href="{{ route('wishlist.list') }}">- {{ $texts['menu']['wishlist_list'] ?? 'Wishlist List' }}</a></li>
                </ul>
            </li>
            <li>
                <a href="{{ route('settings.index') }}">
                    <i class="ti ti-adjustments-horizontal"></i>{{ $texts['menu']['settings'] ?? 'Settings' }}
                </a>
            </li>
            <li>
                <a href="{{ route('logout') }}">
                    <i class="ti ti-logout"></i>{{ $texts['menu']['sign_out'] ?? 'Sign Out' }}
                </a>
            </li>
        </ul>
    </div>
</div>
