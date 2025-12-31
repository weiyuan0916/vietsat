# Kế hoạch thay đổi UI từ Neurapen sang Tiệm Nhà Duy

## Tổng quan
Thay thế toàn bộ UI của pwa-ecommerce bằng UI mới từ neurapen.framer.website, đổi tên từ "Tiệm Nhà Duy" sang "Tiệm Nhà Duy" và cập nhật meta tags cho dịch vụ bán hàng, làm website và tools.

## Các component cần tạo

### 1. Layout & Meta Tags
- **app.blade.php**: Cập nhật meta tags cho "Tiệm Nhà Duy"
  - Description: Dịch vụ bán hàng, thiết kế website, và nhận làm tools theo yêu cầu
  - Keywords: dịch vụ, website, tools, thiết kế, phát triển

### 2. Header Component
- **neurapen-header.blade.php**: Header mới với logo "Tiệm Nhà Duy"
- Gradient text logo
- Navigation menu
- CTA buttons

### 3. Hero Section
- **neurapen-hero.blade.php**: Hero section với gradient background
- Main headline
- Subheadline
- CTA buttons
- Visual elements

### 4. Features Section
- **neurapen-features.blade.php**: Hiển thị các tính năng/dịch vụ
- Grid layout
- Icons/illustrations
- Feature cards

### 5. Pricing Section (nếu cần)
- **neurapen-pricing.blade.php**: Bảng giá dịch vụ
- Pricing cards
- Feature lists

### 6. FAQ Section
- **neurapen-faq.blade.php**: Câu hỏi thường gặp
- Accordion component
- Expandable items

### 7. Footer Component
- **neurapen-footer.blade.php**: Footer mới
- Links
- Social media
- Copyright

### 8. CSS Styles
- **neurapen-styles.css**: Stylesheet mới
- Color variables (gradient colors)
- Typography
- Component styles

## Các file cần xóa
- components/main-slider.blade.php
- components/flash-sale.blade.php
- components/best-seller-list.blade.php
- components/top-product-section.blade.php
- components/promo-banner.blade.php
- components/main-category.blade.php
- components/main-dark-mode.blade.php

## Các file cần cập nhật
- resources/views/layouts/app.blade.php
- resources/views/welcome.blade.php
- resources/views/components/page-header.blade.php (hoặc tạo mới)

## Thứ tự thực hiện
1. ✅ Tạo kế hoạch
2. Cập nhật layout và meta tags
3. Tạo CSS mới với color variables
4. Tạo các component mới
5. Cập nhật welcome.blade.php
6. Xóa các component cũ
7. Test và chỉnh sửa

