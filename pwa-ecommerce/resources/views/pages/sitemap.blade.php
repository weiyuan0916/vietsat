@extends('layouts.rosta')

@section('title', 'Tiệm Nhà Duy | Sitemap')
@section('meta_description', 'Sơ đồ website Tiệm Nhà Duy giúp bạn truy cập nhanh các trang chính.')
@section('og_image', asset('rosta/images/icon-sub-heading.svg'))
@section('canonical_url', route('sitemap'))

@push('structured_data')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "WebPage",
    "name": "Sitemap",
    "description": "Sơ đồ website Tiệm Nhà Duy giúp bạn truy cập nhanh các trang chính.",
    "url": "{{ route('sitemap') }}",
    "inLanguage": "vi-VN"
}
</script>
@endpush

@section('content')
    <style>
        #MainContent {padding-top: 110px;}
        .container.text-only {max-width: 900px; color: #000; padding-bottom: 90px;}
        .text-only h1 {font-size: 7vw; margin-bottom: 20px;}
        .sitemap-group {margin-bottom: 28px;}
        .sitemap-group h2 {font-size: 34px; margin-bottom: 12px;}
        .sitemap-group a {display: block; margin-bottom: 8px; color: #111; text-decoration: underline; text-underline-offset: 4px;}
        @media screen and (max-width: 800px) {
            #MainContent {padding-top: 80px;}
            .text-only h1 {font-size: 50px;}
            .sitemap-group h2 {font-size: 28px;}
        }
    </style>
    <div id="MainContent" tabindex="-1">
        <main data-header-color="dark">
            <div class="container text-only">
                <h1>Sitemap</h1>

                <div class="sitemap-group">
                    <h2>Trang chính</h2>
                    <a href="{{ route('home') }}">Trang chủ</a>
                    <a href="{{ route('about') }}">Về chúng tôi</a>
                    <a href="{{ route('services') }}">Dịch vụ</a>
                    <a href="{{ route('projects') }}">Dự án</a>
                    <a href="{{ route('contact') }}">Liên hệ</a>
                </div>

                <div class="sitemap-group">
                    <h2>Hỗ trợ</h2>
                    <a href="{{ route('book-table') }}">Đặt lịch tư vấn</a>
                    <a href="{{ route('faqs') }}">Câu hỏi thường gặp</a>
                </div>

                <div class="sitemap-group">
                    <h2>Pháp lý</h2>
                    <a href="{{ route('privacy-policy') }}">Chính sách bảo mật</a>
                    <a href="{{ route('terms-of-service') }}">Điều khoản sử dụng</a>
                    <a href="{{ route('sitemap') }}">HTML Sitemap</a>
                    <a href="{{ url('/rosta/sitemap.xml') }}">XML Sitemap</a>
                </div>
            </div>
        </main>
    </div>
@endsection
