@extends('layouts.rosta')

@section('title', 'Tiệm Nhà Duy | 404')
@section('meta_description', 'Trang bạn tìm không tồn tại tại Tiệm Nhà Duy. Hãy quay lại trang chủ để tiếp tục khám phá nội dung.')
@section('meta_robots', 'noindex,follow')
@section('og_image', asset('rosta/images/icon-blockquote.svg'))

@push('structured_data')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "WebPage",
    "name": "404 - Không tìm thấy trang",
    "description": "Trang bạn tìm không tồn tại tại Tiệm Nhà Duy.",
    "url": "{{ url()->current() }}",
    "inLanguage": "vi-VN"
}
</script>
@endpush

@section('content')
    <main id="main-content">
        <div class="page-header parallaxie">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-12">
                        <div class="page-header-box">
                            <h1 class="text-anime-style-3" data-cursor="-opaque">Page Not Found</h1>
                            <nav class="wow fadeInUp">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Trang chủ</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">404</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="our-scrolling-ticker subpages-scrolling-ticker">
            <div class="scrolling-ticker-box">
                <div class="scrolling-content">
                    <span><img src="{{ asset('rosta/images/asterisk-icon.svg') }}" alt="Asterisk icon">Espresso</span>
                    <span><img src="{{ asset('rosta/images/asterisk-icon.svg') }}" alt="Asterisk icon">Americano</span>
                    <span><img src="{{ asset('rosta/images/asterisk-icon.svg') }}" alt="Asterisk icon">Latte</span>
                    <span><img src="{{ asset('rosta/images/asterisk-icon.svg') }}" alt="Asterisk icon">Cappuccino</span>
                    <span><img src="{{ asset('rosta/images/asterisk-icon.svg') }}" alt="Asterisk icon">Mocha</span>
                    <span><img src="{{ asset('rosta/images/asterisk-icon.svg') }}" alt="Asterisk icon">Macchiato</span>
                    <span><img src="{{ asset('rosta/images/asterisk-icon.svg') }}" alt="Asterisk icon">Cold Brew</span>
                </div>
                <div class="scrolling-content">
                    <span><img src="{{ asset('rosta/images/asterisk-icon.svg') }}" alt="Asterisk icon">Espresso</span>
                    <span><img src="{{ asset('rosta/images/asterisk-icon.svg') }}" alt="Asterisk icon">Americano</span>
                    <span><img src="{{ asset('rosta/images/asterisk-icon.svg') }}" alt="Asterisk icon">Latte</span>
                    <span><img src="{{ asset('rosta/images/asterisk-icon.svg') }}" alt="Asterisk icon">Cappuccino</span>
                    <span><img src="{{ asset('rosta/images/asterisk-icon.svg') }}" alt="Asterisk icon">Mocha</span>
                    <span><img src="{{ asset('rosta/images/asterisk-icon.svg') }}" alt="Asterisk icon">Macchiato</span>
                    <span><img src="{{ asset('rosta/images/asterisk-icon.svg') }}" alt="Asterisk icon">Cold Brew</span>
                </div>
            </div>
        </div>

        <div class="error-page">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="error-page-content">
                            <div class="section-title">
                                <h2 class="text-anime-style-3" data-cursor="-opaque">Oops! Không tìm thấy trang</h2>
                            </div>
                            <div class="error-page-content-body">
                                <p class="wow fadeInUp" data-wow-delay="0.25s">
                                    Trang bạn đang tìm có thể đã bị đổi đường dẫn hoặc không còn tồn tại.
                                    Hãy quay về trang chủ để tiếp tục trải nghiệm.
                                </p>
                                <a class="btn-default wow fadeInUp" data-wow-delay="0.5s" href="{{ url('/') }}">
                                    <span>Quay về trang chủ</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
