<footer class="main-footer">
    <div class="container onyx-footer">
        <div class="onyx-grid onyx-grid-top">
            <div class="span-2">
                <h3>Đăng ký nhận tin</h3>
                <p>Nhận thông tin sản phẩm mới, ưu đãi và mẹo sử dụng nông sản hữu ích từ Tiệm Nhà Duy.</p>
                <form class="onyx-newsletter" action="{{ route('contact.send') }}" method="post">
                    @csrf
                    <input type="email" name="email" placeholder="Nhập email của bạn" required>
                    <button type="submit">Đăng ký</button>
                </form>
            </div>
            <div class="span-1">
                <h3>Mua sắm</h3>
                <a href="{{ route('services') }}">Cà phê</a>
                <a href="{{ route('services') }}">Mắc ca</a>
                <a href="{{ route('services') }}">Tiêu</a>
                <a href="{{ route('services') }}">Bơ</a>
            </div>
            <div class="span-1">
                <h3>Hỗ trợ</h3>
                <a href="{{ route('contact') }}">Trung tâm hỗ trợ</a>
                <a href="mailto:support@tiemnhaduy.com">Gửi email</a>
                <a href="{{ route('contact') }}">Nhắn tin nhanh</a>
                <p>Thứ Hai - Thứ Sáu<br>9:00 - 17:00</p>
            </div>
        </div>
        <div class="onyx-mark" aria-label="TND">
            <span>T</span>
            <span>N</span>
            <span>D</span>
        </div>
        <div class="onyx-grid onyx-grid-bottom">
            <div class="span-1">
                <h3>Khu vực</h3>
                <a href="{{ route('about') }}">Chư Sê - Gia Lai</a>
                <a href="{{ route('about') }}">Pleiku</a>
                <a href="{{ route('about') }}">Đắk Lắk</a>
                <a href="{{ route('about') }}">Toàn quốc</a>
            </div>
            <div class="span-1">
                <h3>Giới thiệu</h3>
                <a href="{{ route('about') }}">Cam kết chất lượng</a>
                <a href="{{ route('projects') }}">Tin tức nông sản</a>
                <a href="{{ route('about') }}">Câu chuyện thương hiệu</a>
                <a href="{{ route('contact') }}">Liên hệ hợp tác</a>
            </div>
            <div class="span-1">
                <h3>Quà tặng</h3>
                <a href="{{ route('book-table') }}">Hộp quà đặc sản</a>
                <a href="{{ route('book-table') }}">Gói định kỳ</a>
                <a href="{{ route('services') }}">Sản phẩm đề xuất</a>
                <a href="{{ route('book-table') }}">Phiếu quà tặng</a>
            </div>
            <div class="span-1">
                <h3>Kiến thức</h3>
                <a href="{{ route('faqs') }}">Hướng dẫn sử dụng</a>
                <a href="{{ route('services') }}">Chọn loại phù hợp</a>
                <a href="{{ route('faqs') }}">Mẹo bảo quản</a>
                <a href="{{ route('faqs') }}">Cách pha cà phê ngon</a>
            </div>
            <div class="span-1">
                <h3>Dịch vụ</h3>
                <a href="{{ route('services') }}">Bán sỉ</a>
                <a href="{{ route('services') }}">Tư vấn menu</a>
                <a href="{{ route('services') }}">Tư vấn vận hành</a>
                <a href="{{ route('contact') }}">Đóng gói theo yêu cầu</a>
            </div>
        </div>
        <div class="onyx-colophon">
            <div class="flex-between">
                <p>Chất lượng thật, giá trị thật cho mỗi nhà.</p>
                <p>Copyright © 2026 Tiệm Nhà Duy. All Rights Reserved.</p>
            </div>
            <hr>
            <div class="flex-between">
                <div class="footer-left-tools">
                    <p class="language-switcher">
                        <a href="{{ url()->current() }}" aria-label="Switch to Vietnamese">🇻🇳 VI</a>
                        <a href="{{ url()->current() }}" aria-label="Switch to English">🇬🇧 EN</a>
                        <a href="{{ url()->current() }}" aria-label="Switch to French">🇫🇷 FR</a>
                    </p>
                    <p class="social">
                        <a href="https://www.facebook.com/tiemnhaduy"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="https://www.instagram.com/tiemnhaduy"><i class="fa-brands fa-instagram"></i></a>
                        <a href="https://maps.app.goo.gl/JXTdYnauTKkTRdbi7"><i class="fa-solid fa-location-dot"></i></a>
                        <a href="https://www.youtube.com/@tiemnhaduy"><i class="fa-brands fa-youtube"></i></a>
                    </p>
                </div>
                <p>
                    <a href="{{ route('terms-of-service') }}">Điều khoản sử dụng</a>
                    <a href="{{ route('privacy-policy') }}">Chính sách bảo mật</a>
                    <a href="{{ route('sitemap') }}">Sitemap</a>
                </p>
            </div>
        </div>
    </div>
</footer>