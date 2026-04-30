@extends('layouts.rosta')

@section('title', 'Dịch vụ')
@section('meta_description', 'Các dịch vụ chính của Tiệm Nhà Duy.')
@section('og_image', asset('rosta/images/icon-service-1.svg'))
@section('canonical_url', route('services'))

@push('structured_data')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "WebPage",
    "name": "Dịch vụ",
    "description": "Các dịch vụ chính của Tiệm Nhà Duy.",
    "url": "{{ route('services') }}",
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
            "name": "Dịch vụ",
            "item": "{{ route('services') }}"
        }
    ]
}
</script>
@endpush

@section('content')
    @include('pages.partials.rosta.page-header', ['title' => 'dịch vụ'])
    <div class="page-services">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6">
                    <div class="service-item wow fadeInUp">
                        <div class="icon-box"><img src="{{ asset('rosta/images/icon-service-1.svg') }}" alt="Service"></div>
                        <div class="service-content">
                            <h3>Tư vấn giao diện & UX</h3>
                            <p>Thiết kế lại giao diện bán hàng theo định hướng thương hiệu và chuyển đổi.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="service-item wow fadeInUp" data-wow-delay="0.2s">
                        <div class="icon-box"><img src="{{ asset('rosta/images/icon-service-2.svg') }}" alt="Service"></div>
                        <div class="service-content">
                            <h3>Phát triển frontend</h3>
                            <p>Tách component, chuẩn hóa layout và tối ưu hiệu năng hiển thị.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="service-item wow fadeInUp" data-wow-delay="0.4s">
                        <div class="icon-box"><img src="{{ asset('rosta/images/icon-service-3.svg') }}" alt="Service"></div>
                        <div class="service-content">
                            <h3>Vận hành & bảo trì</h3>
                            <p>Hỗ trợ cập nhật định kỳ, sửa lỗi và cải tiến giao diện liên tục.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
