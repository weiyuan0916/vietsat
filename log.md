# log.md — Lịch sử thay đổi dự án

## 2025-03-19 — Thiết kế mới trang Giỏ hàng (Pencil)

### File thay đổi
- **pencil/appdesign.pen**

### Thay đổi
- **Thêm frame mới "Giỏ hàng"** (id: 97zMn), copy từ frame Home, đặt tại (1004, 974).
- **Header:**
  - Nút back (chevron-left) bên trái.
  - Tiêu đề "Giỏ hàng" giữa, font Outfit 18px bold.
  - Link "Xoá tất cả" bên phải, màu secondary.
- **Danh sách giỏ hàng:**
  - Card item: ảnh 72×72 (placeholder), tên gói + giá (màu accent), **bộ chọn số lượng nằm ngang** (nút −, số, nút +) với nút rõ ràng 36×36, viền nhẹ; nút xóa (trash-2) bên phải.
  - Card có stroke nhẹ `$border-subtle` để tách với nền.
- **Tổng cộng:** Dòng "Tổng cộng" + "8.000.000 đ" (accent, bold).
- **CTA:** Nút "Thanh toán" full width, 52px cao, màu accent, icon credit-card.
- **Tab Bar:** Tab "Đơn hàng" (ĐƠN HÀNG) đang active (nền accent, chữ trắng); tab HOME inactive.

### Lý do
- Sửa lỗi UX/UI so với bản cũ: số lượng dọc → ngang dễ bấm, thêm nút Thanh toán, thêm ảnh/xóa từng item, tab đúng khi ở giỏ hàng.

### 2025-03-19 — Tối ưu vị trí nút CTA

**Thay đổi:**
- Sửa layout Giỏ hàng: dùng `layout: vertical` để Spacer (`fill_container`) đẩy CTA "Thanh toán" xuống sát Tab Bar.
- Header "Giỏ hàng" căn giữa, "Xoá tất cả" bên phải.

**Kết quả:** Nút CTA không còn bị che khuất, luôn hiển thị ở vị trí cuối màn hình, trên Tab Bar.

### 2025-03-19 — Áp dụng thiết kế Giỏ hàng vào code

**File thay đổi:**
- `frontend/pages/pages/cart.html`
- `frontend/js/app.js`
- `frontend/css/custom-vietsat.css`

**Thay đổi:**
- **Header:** "Giỏ hàng" căn giữa, "Xoá tất cả" bên phải, nút back bên trái
- **Cart Item:** Ảnh placeholder 72×72, tên + giá, bộ chọn số lượng **nằm ngang** (nút −, số, nút +), nút xóa bên phải
- **Tổng cộng:** "Tổng cộng" + giá (accent, bold)
- **CTA "Thanh toán":** Icon credit-card, ghim ở cuối trang (không còn Tab Bar)
- **CSS:** Thêm `.vs-cart-page` flexbox layout để nội dung co giãn đúng
- **Tab Bar:** Đã xóa khỏi thiết kế Giỏ hàng - trang không còn Tab Bar

### 2025-03-19 — Đơn giản hóa điều hướng

**Thay đổi:**
- Ẩn Tab Bar trên các trang: Giỏ hàng (cart), Chi tiết dịch vụ (service)
- Tab Bar chỉ hiển thị trên 4 trang chính: Home, Services, Orders, Profile
- Bottom bar của các trang chi tiết đặt tại `bottom: 0` thay vì `bottom: 90px`
- Thêm `padding-bottom` cho page content để nội dung không bị che bởi bottom bar

**CSS thay đổi:**
```css
/* Ẩn Tab Bar trên trang chi tiết */
.vs-detail-page~.vs-tab-bar-wrap,
.vs-auth-page~.vs-tab-bar-wrap,
.vs-cart-page~.vs-tab-bar-wrap,
.vs-service-page~.vs-tab-bar-wrap {
  display: none;
}
```

**HTML thay đổi:**
- cart.html: Thêm class `vs-detail-page` để ẩn Tab Bar
- cart.html: Bottom bar đặt tại `bottom: 0`

**Kết quả UX:**
- Trang Giỏ hàng và Chi tiết dịch vụ: Tab Bar ẩn, CTA/button đặt sát mép dưới
- 4 trang chính: Tab Bar hiển bình thường
- Đồng nhất trải nghiệm trên toàn ứng dụng

### 2025-03-19 — Tự động ẩn/hiện Tab Bar bằng JavaScript

**Vấn đề:** CSS sibling selector (~) không hoạt động vì cấu trúc DOM của Framework7 (các page nằm trong swiper-slide)

**Giải pháp:** Dùng JavaScript để kiểm tra URL và ẩn/hiện Tab Bar

**Thay đổi:**
- Sửa hàm `enforceTabBarVisibility()` trong `frontend/js/app.js`
- Thêm logic kiểm tra 4 tab chính (`/home/`, `/services/`, `/orders/`, `/profile/`)
- Thêm logic kiểm tra các trang cần ẩn Tab Bar (`/cart/`, `/service/`, `/signin`, `/signup`, `/forgot-password`)
- Tab Bar sẽ tự động ẩn khi vào Giỏ hàng, Chi tiết dịch vụ, và các trang auth
- Tab Bar sẽ hiển thị khi ở 4 tab chính

### 2025-03-19 — Tối ưu Service Detail

**Thay đổi:**
- Thêm `layout: vertical` + Content `fill_container` để CTA "Mua ngay" được ghim xuống dưới cùng màn hình.

**Kết quả:** CTA luôn ở cuối màn, tối ưu UX.

### 2025-03-19 — Fix Tab Bar vẫn che giỏ hàng + Fix 500 khi thêm vào giỏ

**Vấn đề 1:** Tab Bar vẫn hiển thị trên trang Giỏ hàng, che dòng "Tổng cộng" và nút "Thanh toán".

**Nguyên nhân:** `cleanPath` lấy từ hash bị sai — với hash `#!/cart/`, code cũ dùng `.split('#')[0]` nên ra chuỗi rỗng, không khớp `/cart/`.

**Thay đổi (frontend/js/app.js):**
- Sửa `cleanPath`: dùng `hash.replace(/^#!?/, '')` để lấy path sau `#` hoặc `#!`, chuẩn hóa thêm `/` ở đầu.
- Danh sách ẩn Tab Bar: `/cart`, `/service`, `/signin`, `/signup`, `/forgot-password` (khớp cả `/cart/`, `/cart?…`).
- Gọi `enforceTabBarVisibility()` trong `page:init` (mọi trang) và trong `page:init` của trang `cart` để đảm bảo ẩn Tab Bar ngay khi vào giỏ hàng.

**Vấn đề 2:** POST `/api/v1/cart/items` trả 500, không thêm được sản phẩm vào giỏ.

**Thay đổi (backend):**
- **CartService::addItem:** Khi giao dịch thành công, luôn reload cart và trả về `['success' => true, 'data' => formatCart($cart)]` thay vì chỉ `['success' => true]`. Chỉ return sớm khi `$result['success'] === false`. Thêm log `trace` khi exception.
- **CartController::addItem:** Dùng `$request->input('service_id')` và cast sang int; bắt mọi `\Throwable`, log lỗi và trả JSON 500 với message "Lỗi máy chủ. Vui lòng thử lại sau."; nếu `$result['data']` null thì `$cart->refresh()->load('items.service')` rồi format lại để response luôn có `data`.

**Lưu ý:** Nếu server production (tiemnhaduy.com) vẫn 500 sau khi deploy, cần kiểm tra đã chạy migration (cart_items có cột `service_id`, `product_id` nullable) và xem log Laravel để biết lỗi cụ thể.

### 2025-03-19 — Fix nút giỏ hàng chỉ vào được một lần

**Vấn đề:** Click icon giỏ hàng (vs-header-cart) lần đầu mở cart, thoát ra rồi click lại thì không vào lại được.

**Nguyên nhân:** Framework7 router cache trang cart; lần navigate thứ hai tới `/cart/` dùng bản cache nên không hiển thị đúng. Ngoài ra có hai handler cùng xử lý click (document + $$(".swiper-slide a[data-href]")) gây navigate trùng.

**Thay đổi (frontend/js/app.js):**
- Với link có `data-href` hoặc `href` trỏ tới overlay (`/cart/`, `/service/`, `/signin/`, `/signup/`), gọi `router.navigate(url, { ignoreCache: true })` để mỗi lần click đều load lại trang, không dùng cache.
- Xóa handler trùng `$$(".swiper-slide a[data-href]").on("click", ...)` và phần bind lại trong `page:init`; chỉ giữ một nơi xử lý tại document click (đã có sẵn) để tránh navigate 2 lần và hành vi lỗi khi click lần 2.
