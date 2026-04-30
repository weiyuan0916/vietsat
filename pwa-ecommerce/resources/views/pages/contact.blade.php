@extends('layouts.rosta')

@section('title', 'Liên hệ')
@section('meta_description', 'Liên hệ Tiệm Nhà Duy để nhận tư vấn nhanh.')
@section('og_image', asset('rosta/images/icon-mail-accent.svg'))
@section('canonical_url', route('contact'))

@push('structured_data')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "ContactPage",
    "name": "Liên hệ",
    "description": "Liên hệ Tiệm Nhà Duy để nhận tư vấn nhanh.",
    "url": "{{ route('contact') }}",
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
            "name": "Liên hệ",
            "item": "{{ route('contact') }}"
        }
    ]
}
</script>
@endpush

@section('content')
    @include('pages.partials.rosta.page-header', ['title' => 'liên hệ'])
    <div class="page-contact-us">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="contact-information">
                        <div class="section-title">
                            <h3 class="wow fadeInUp">liên hệ</h3>
                            <h2 class="text-anime-style-3" data-cursor="-opaque">Gửi yêu cầu cho chúng tôi</h2>
                        </div>
                        <div class="contact-info-body">
                            <div class="contact-info-item">
                                <div class="icon-box"><img src="{{ asset('rosta/images/icon-phone-accent.svg') }}" alt="Phone"></div>
                                <div class="contact-item-content"><p><a href="tel:+84901234567">+84 901 234 567</a></p></div>
                            </div>
                            <div class="contact-info-item">
                                <div class="icon-box"><img src="{{ asset('rosta/images/icon-mail-accent.svg') }}" alt="Mail"></div>
                                <div class="contact-item-content"><p><a href="mailto:support@tiemnhaduy.com">support@tiemnhaduy.com</a></p></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
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
                                <div class="form-group col-md-12 mb-4">
                                    <textarea name="message" class="form-control" rows="4" placeholder="Nội dung" required></textarea>
                                </div>
                                <div class="col-lg-12">
                                    <button type="submit" class="btn-default">Gửi liên hệ</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
