<header class="site-header">
    <div class="container d-flex align-items-center justify-content-between py-3">
        <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
            <img src="{{ asset('frontend/img/core-img/logo-small.png') }}" alt="Tiệm Nhà Duy logo" width="48" height="48" loading="lazy">
            <span class="ms-2 h5 mb-0">Tiệm Nhà Duy</span>
        </a>
        @php
            $homeUrl = \Illuminate\Support\Facades\Route::has('home') ? route('home') : url('/');
            $servicesUrl = \Illuminate\Support\Facades\Route::has('services') ? route('services') : url('/services');
            $projectsUrl = \Illuminate\Support\Facades\Route::has('projects') ? route('projects') : url('/projects');
            $contactUrl = \Illuminate\Support\Facades\Route::has('contact') ? route('contact') : url('/contact');
        @endphp
        <nav class="site-nav">
            <ul class="nav">
                <li class="nav-item"><a class="nav-link" href="{{ $homeUrl }}">Trang chủ</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ $servicesUrl }}">Dịch vụ</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ $projectsUrl }}">Dự án</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ $contactUrl }}">Liên hệ</a></li>
            </ul>
        </nav>
    </div>
</header>


