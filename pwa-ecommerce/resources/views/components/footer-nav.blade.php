<!-- Footer Nav -->
<div class="footer-nav-area" id="footerNav">
    <div class="suha-footer-nav">
        <ul class="h-100 d-flex align-items-center justify-content-between ps-0 d-flex rtl-flex-d-row-r">
            <li>
                <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">
                    <i class="ti ti-home"></i>Home
                </a>
            </li>
            <li>
                <a href="{{ route('messages.index') }}" class="{{ request()->routeIs('messages.*') ? 'active' : '' }}">
                    <i class="ti ti-message"></i>Chat
                </a>
            </li>
            <li>
                <a href="{{ route('cart.index') }}" class="{{ request()->routeIs('cart.*') ? 'active' : '' }}">
                    <i class="ti ti-basket"></i>Cart
                </a>
            </li>
            <li>
                <a href="{{ route('settings.index') }}" class="{{ request()->routeIs('settings.*') ? 'active' : '' }}">
                    <i class="ti ti-settings"></i>Settings
                </a>
            </li>
            <li>
                <a href="{{ route('pages.index') }}" class="{{ request()->routeIs('pages.*') ? 'active' : '' }}">
                    <i class="ti ti-heart"></i>Pages
                </a>
            </li>
        </ul>
    </div>
</div>

