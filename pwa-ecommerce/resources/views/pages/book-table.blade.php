@extends('layouts.rosta')

@section('title', 'Đặt lịch tư vấn')
@section('meta_description', 'Đặt lịch tư vấn sản phẩm tại Tiệm Nhà Duy.')
@section('og_image', asset('rosta/images/icon-phone-accent.svg'))
@section('canonical_url', route('book-table'))

@push('structured_data')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "WebPage",
    "name": "Đặt lịch tư vấn",
    "description": "Đặt lịch tư vấn sản phẩm tại Tiệm Nhà Duy.",
    "url": "{{ route('book-table') }}",
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
            "name": "Đặt lịch tư vấn",
            "item": "{{ route('book-table') }}"
        }
    ]
}
</script>
@endpush

@section('content')
    @include('pages.partials.rosta.page-header', ['title' => 'đặt lịch tư vấn'])
    <div class="page-contact-us">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-5">
                    <div class="contact-information">
                        <div class="section-title">
                            <h3 class="wow fadeInUp">đặt lịch</h3>
                            <h2 class="text-anime-style-3" data-cursor="-opaque">Để lại thông tin, chúng tôi gọi lại nhanh</h2>
                            <p class="wow fadeInUp" data-wow-delay="0.2s">Phù hợp cho khách mua lẻ, mua sỉ, đặt combo quà tặng hoặc cần tư vấn sản phẩm theo nhu cầu.</p>
                        </div>
                        <div class="contact-info-body">
                            <div class="contact-info-item">
                                <div class="icon-box"><img src="{{ asset('rosta/images/icon-phone-accent.svg') }}" alt="Phone"></div>
                                <div class="contact-item-content"><p><a href="tel:+84981314516">+84 981 314 516</a></p></div>
                            </div>
                            <div class="contact-info-item">
                                <div class="icon-box"><img src="{{ asset('rosta/images/icon-mail-accent.svg') }}" alt="Mail"></div>
                                <div class="contact-item-content"><p><a href="mailto:support@tiemnhaduy.com">support@tiemnhaduy.com</a></p></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="contact-form">
                        <form action="{{ route('contact.send') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="form-group col-md-6 mb-4">
                                    <input type="text" name="name" class="form-control" placeholder="Họ tên" required>
                                </div>
                                <div class="form-group col-md-6 mb-4">
                                    <input type="email" name="email" class="form-control" placeholder="Email" required>
                                </div>
                                <div class="form-group col-md-6 mb-4">
                                    <input type="text" name="phone" class="form-control" placeholder="Số điện thoại">
                                </div>
                                <div class="form-group col-md-6 mb-4">
                                    <input type="text" name="subject" class="form-control" placeholder="Nhu cầu tư vấn">
                                </div>
                                <div class="form-group col-md-12 mb-4">
                                    <textarea name="message" class="form-control" rows="4" placeholder="Mô tả chi tiết" required></textarea>
                                </div>
                                <div class="col-lg-12">
                                    <button type="submit" class="btn-default">Gửi yêu cầu</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
