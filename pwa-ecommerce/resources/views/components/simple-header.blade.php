@props([
    'title' => 'Page',
    'backUrl' => null,
    'showBackButton' => true,
    'showNavbarToggler' => true
])

<!-- Header Area -->
<div class="header-area" id="headerArea">
    <div class="container h-100 d-flex align-items-center justify-content-between rtl-flex-d-row-r">
        <!-- Back Button -->
        @if($showBackButton)
            <div class="back-button me-2">
                <a href="{{ $backUrl ?? url()->previous() }}">
                    <i class="ti ti-arrow-left"></i>
                </a>
            </div>
        @endif
        
        <!-- Page Title -->
        <div class="page-heading">
            <h6 class="mb-0">{{ $title }}</h6>
        </div>
        
        <!-- Navbar Toggler -->
        @if($showNavbarToggler)
            <div class="suha-navbar-toggler ms-2" data-bs-toggle="offcanvas" data-bs-target="#suhaOffcanvas" aria-controls="suhaOffcanvas">
                <div><span></span><span></span><span></span></div>
            </div>
        @endif
    </div>
</div>

