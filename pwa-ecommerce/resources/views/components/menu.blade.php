@props(['user' => null, 'notificationCount' => 0])

<div class="offcanvas offcanvas-start suha-offcanvas-wrap" tabindex="-1" id="suhaOffcanvas" aria-labelledby="suhaOffcanvasLabel">
    <!-- Close button -->
    <button class="btn-close btn-close-white" type="button" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    
    <!-- Offcanvas body -->
    <div class="offcanvas-body">
        <!-- Sidenav Profile -->
        <div class="sidenav-profile">
            <div class="user-profile">
                <img src="{{ $user?->avatar ?? asset('frontend/img/bg-img/9.jpg') }}" 
                     alt="{{ $user?->name ?? 'Guest' }}">
            </div>
            <div class="user-info">
                @auth
                    <h5 class="user-name mb-1 text-white">{{ $user?->name ?? 'User' }}</h5>
                    <p class="available-balance text-white">
                        Current Balance $<span class="counter">{{ number_format($user?->balance ?? 0, 2) }}</span>
                    </p>
                @else
                    <h5 class="user-name mb-1 text-white">Welcome Guest</h5>
                    <p class="available-balance text-white">
                        <a href="{{ route('login') }}" class="text-white">Login</a> or 
                        <a href="{{ route('register') }}" class="text-white">Register</a>
                    </p>
                @endauth
            </div>
        </div>
        
        <!-- Sidenav Nav -->
        <ul class="sidenav-nav ps-0">
            <li>
                <a href="{{ route('profile.show') }}">
                    <i class="ti ti-user"></i>My Profile
                </a>
            </li>
            <li>
                <a href="{{ route('notifications.index') }}">
                    <i class="ti ti-bell-ringing lni-tada-effect"></i>Notifications
                    @if($notificationCount > 0)
                        <span class="ms-1 badge badge-warning">{{ $notificationCount }}</span>
                    @endif
                </a>
            </li>
            <li class="suha-dropdown-menu">
                <a href="#"><i class="ti ti-building-store"></i>Shop Pages</a>
                <ul>
                    <li><a href="{{ route('shop.grid') }}">- Shop Grid</a></li>
                    <li><a href="{{ route('shop.list') }}">- Shop List</a></li>
                    <li><a href="{{ route('products.featured') }}">- Featured Products</a></li>
                    <li><a href="{{ route('products.flash-sale') }}">- Flash Sale</a></li>
                </ul>
            </li>
            <li>
                <a href="{{ route('pages.index') }}">
                    <i class="ti ti-notebook"></i>All Pages
                </a>
            </li>
            <li class="suha-dropdown-menu">
                <a href="{{ route('wishlist.index') }}">
                    <i class="ti ti-heart"></i>My Wishlist
                </a>
                <ul>
                    <li><a href="{{ route('wishlist.grid') }}">- Wishlist Grid</a></li>
                    <li><a href="{{ route('wishlist.list') }}">- Wishlist List</a></li>
                </ul>
            </li>
            <li>
                <a href="{{ route('settings.index') }}">
                    <i class="ti ti-adjustments-horizontal"></i>Settings
                </a>
            </li>
            @auth
                <li>
                    <form action="{{ route('logout') }}" method="POST" id="logoutForm">
                        @csrf
                        <a href="#" onclick="event.preventDefault(); document.getElementById('logoutForm').submit();">
                            <i class="ti ti-logout"></i>Sign Out
                        </a>
                    </form>
                </li>
            @else
                <li>
                    <a href="{{ route('login') }}">
                        <i class="ti ti-login"></i>Sign In
                    </a>
                </li>
            @endauth
        </ul>
    </div>
</div>

