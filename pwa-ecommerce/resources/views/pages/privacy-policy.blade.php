@extends('layouts.rosta')

@section('title', 'Tiệm Nhà Duy | Chính Sách Bảo Mật')
@section('meta_description', 'Chính sách bảo mật của Tiệm Nhà Duy về thu thập, sử dụng và bảo vệ thông tin cá nhân của khách hàng.')
@section('og_image', asset('rosta/images/icon-our-mission.svg'))
@section('canonical_url', route('privacy-policy'))

@push('structured_data')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "WebPage",
    "name": "Chính sách bảo mật",
    "description": "Chính sách bảo mật của Tiệm Nhà Duy về thu thập, sử dụng và bảo vệ thông tin cá nhân của khách hàng.",
    "url": "{{ route('privacy-policy') }}",
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
            "name": "Chính sách bảo mật",
            "item": "{{ route('privacy-policy') }}"
        }
    ]
}
</script>
@endpush

@section('content')
    <style>
        #MainContent {padding-top: 110px;}
        .container.text-only {max-width: 900px; color: #000; padding-bottom: 90px;}
        .text-only h1 {font-size: 7vw; margin-bottom: 20px;}
        .policy-content p {margin-bottom: 16px; line-height: 1.8;}
        @media screen and (max-width: 800px) {
            #MainContent {padding-top: 80px;}
            .text-only h1 {font-size: 50px;}
        }
    </style>
    <div id="MainContent" tabindex="-1">
        <main data-header-color="dark">
            <div class="container text-only">
                <div class="policy-content">
                    <h1>Chính sách bảo mật</h1>
                    <p><strong>1. Mục đích thu thập thông tin</strong><br>Tiệm Nhà Duy thu thập thông tin cần thiết như họ tên, số điện thoại, email và địa chỉ nhận hàng để xử lý đơn hàng, chăm sóc khách hàng và nâng cao chất lượng dịch vụ.</p>
                    <p><strong>2. Phạm vi sử dụng thông tin</strong><br>Thông tin cá nhân được sử dụng để xác nhận đơn hàng, giao hàng, hỗ trợ sau bán, gửi thông báo liên quan đến đơn hàng hoặc chương trình ưu đãi khi khách hàng đồng ý nhận tin.</p>
                    <p><strong>3. Thời gian lưu trữ dữ liệu</strong><br>Dữ liệu được lưu trong thời gian cần thiết để phục vụ giao dịch, đối soát và tuân thủ quy định pháp luật. Khi không còn cần thiết, dữ liệu sẽ được xóa hoặc ẩn danh theo quy trình nội bộ.</p>
                    <p><strong>4. Cam kết bảo mật</strong><br>Chúng tôi áp dụng biện pháp kỹ thuật và quản trị phù hợp để bảo vệ dữ liệu khỏi truy cập trái phép, thất thoát hoặc lạm dụng thông tin.</p>
                    <p><strong>5. Chia sẻ thông tin</strong><br>Tiệm Nhà Duy không mua bán thông tin cá nhân. Dữ liệu chỉ được chia sẻ cho đối tác vận chuyển, cổng thanh toán hoặc cơ quan có thẩm quyền khi thật sự cần thiết để hoàn tất giao dịch và tuân thủ pháp luật.</p>
                    <p><strong>6. Quyền của khách hàng</strong><br>Khách hàng có quyền yêu cầu xem, chỉnh sửa hoặc xóa thông tin cá nhân; đồng thời có thể từ chối nhận thông tin tiếp thị bất kỳ lúc nào.</p>
                    <p><strong>7. Cookie và công cụ đo lường</strong><br>Website có thể sử dụng cookie để ghi nhớ trải nghiệm và phân tích truy cập. Bạn có thể tùy chỉnh trình duyệt để từ chối cookie theo nhu cầu.</p>
                    <p><strong>8. Liên hệ xử lý dữ liệu cá nhân</strong><br>Mọi yêu cầu về bảo mật thông tin vui lòng liên hệ qua email <a href="mailto:support@tiemnhaduy.com">support@tiemnhaduy.com</a>.</p>
                    <p><strong>9. Cập nhật chính sách</strong><br>Chính sách này có thể được cập nhật theo nhu cầu vận hành hoặc yêu cầu pháp lý. Phiên bản mới nhất luôn được công bố tại trang này.</p>
                </div>
            </div>
        </main>
    </div>
@endsection
