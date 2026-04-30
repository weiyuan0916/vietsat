@extends('layouts.rosta')

@section('title', 'Về Tiệm Nhà Duy')
@section('meta_description', 'Giới thiệu về Tiệm Nhà Duy và hành trình phát triển.')
@section('og_image', asset('rosta/images/about-restaurant-bg-image.svg'))
@section('canonical_url', route('about'))

@push('structured_data')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "WebPage",
    "name": "Về Tiệm Nhà Duy",
    "description": "Giới thiệu về Tiệm Nhà Duy và hành trình phát triển.",
    "url": "{{ route('about') }}",
    "inLanguage": "vi-VN"
}
</script>
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "BreadcrumbList",
    "itemListElement": [
        {
            "@@type": "ListItem",
            "position": 1,
            "name": "Trang chủ",
            "item": "{{ route('home') }}"
        },
        {
            "@@type": "ListItem",
            "position": 2,
            "name": "Về chúng tôi",
            "item": "{{ route('about') }}"
        }
    ]
}
</script>
@endpush

@section('content')
    @include('pages.partials.rosta.page-header', ['title' => 'về chúng tôi'])
    <div class="about-us">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="about-us-content">
                        <div class="section-title">
                            <h3 class="wow fadeInUp">về chúng tôi</h3>
                            <h2 class="text-anime-style-3" data-cursor="-opaque">Mang nông sản sạch và cà phê chất lượng đến mọi gia đình</h2>
                            <p class="wow fadeInUp" data-wow-delay="0.2s">Tiệm Nhà Duy tập trung vào nguồn gốc rõ ràng, sản phẩm an toàn và trải nghiệm mua sắm minh bạch.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <figure class="image-anime">
                        <img src="{{ asset('rosta/images/our-story-image.jpg') }}" alt="Our story">
                    </figure>
                </div>
            </div>
        </div>
    </div>
@endsection
