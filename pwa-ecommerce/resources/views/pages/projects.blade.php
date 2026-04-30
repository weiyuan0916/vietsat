@extends('layouts.rosta')

@section('title', 'Dự án')
@section('meta_description', 'Một số dự án tiêu biểu của Tiệm Nhà Duy.')
@section('og_image', asset('rosta/images/icon-project-detail-1.svg'))
@section('canonical_url', route('projects'))

@push('structured_data')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "WebPage",
    "name": "Dự án",
    "description": "Một số dự án tiêu biểu của Tiệm Nhà Duy.",
    "url": "{{ route('projects') }}",
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
            "name": "Dự án",
            "item": "{{ route('projects') }}"
        }
    ]
}
</script>
@endpush

@section('content')
    @include('pages.partials.rosta.page-header', ['title' => 'dự án'])
    <div class="page-projects">
        <div class="container">
            <div class="row project-item-boxes align-items-center">
                <div class="col-lg-4 col-md-6 project-item-box">
                    <div class="project-item wow fadeInUp">
                        <div class="project-image">
                            <figure class="image-anime"><img src="{{ asset('rosta/images/project-1.jpg') }}" alt="Project 1"></figure>
                        </div>
                        <div class="project-content"><h3>Landing page nông sản</h3></div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 project-item-box">
                    <div class="project-item wow fadeInUp" data-wow-delay="0.2s">
                        <div class="project-image">
                            <figure class="image-anime"><img src="{{ asset('rosta/images/project-2.jpg') }}" alt="Project 2"></figure>
                        </div>
                        <div class="project-content"><h3>Trang danh mục sản phẩm</h3></div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 project-item-box">
                    <div class="project-item wow fadeInUp" data-wow-delay="0.4s">
                        <div class="project-image">
                            <figure class="image-anime"><img src="{{ asset('rosta/images/project-3.jpg') }}" alt="Project 3"></figure>
                        </div>
                        <div class="project-content"><h3>UI quản lý đơn hàng</h3></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
