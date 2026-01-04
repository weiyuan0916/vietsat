# PC Information Management

Hệ thống quản lý thông tin PC/client với API và Filament Admin Panel.

## Tổng quan

Hệ thống này cho phép:
- Thu thập thông tin PC qua API
- Quản lý dữ liệu PC trong admin panel
- Tự động cập nhật thông tin khi PC kết nối lại

## Cấu trúc Database

Bảng `pc_infos`:
- `id`: Primary key
- `host_name`: Tên máy (nullable)
- `user_name`: Tên người dùng (nullable)
- `password`: Mật khẩu (nullable)
- `local_ip_address`: IP local (nullable)
- `public_ip_address`: IP public (nullable)
- `created_at`: Thời gian tạo
- `updated_at`: Thời gian cập nhật

## API Endpoints

### 1. POST /api/v1/pc-infos
Lưu thông tin PC mới hoặc cập nhật thông tin hiện có.

**Request Body:**
```json
{
  "host_name": "DESKTOP-ABC123",
  "user_name": "john_doe",
  "password": "encrypted_password",
  "local_ip_address": "192.168.1.100",
  "public_ip_address": "203.0.113.1"
}
```

**Response:**
```json
{
  "success": true,
  "message": "PC information stored successfully",
  "data": {
    "pc_info": {...},
    "created": true
  }
}
```

### 2. GET /api/v1/pc-infos
Lấy danh sách thông tin PC với phân trang và lọc.

**Query Parameters:**
- `host_name`: Lọc theo tên máy
- `user_name`: Lọc theo tên người dùng
- `ip_address`: Lọc theo địa chỉ IP
- `sort_by`: Sắp xếp theo field (default: created_at)
- `sort_direction`: asc/desc (default: desc)
- `per_page`: Số item mỗi trang (default: 15)

### 3. GET /api/v1/pc-infos/{id}
Lấy thông tin chi tiết của một PC.

### 4. PUT/PATCH /api/v1/pc-infos/{id}
Cập nhật thông tin PC.

### 5. DELETE /api/v1/pc-infos/{id}
Xóa thông tin PC.

### 6. GET /api/v1/pc-infos/statistics/overview
Lấy thống kê tổng quan.

## Filament Admin Panel

### Menu: System Management > PC Information

**Các tính năng:**
- Xem danh sách PC với thông tin đầy đủ
- Tìm kiếm và lọc theo nhiều tiêu chí
- Thêm/sửa/xóa thông tin PC
- Export dữ liệu ra CSV
- Thống kê tổng quan

**Các filter có sẵn:**
- Has Host Name: Chỉ hiển thị PC có tên máy
- Has User Name: Chỉ hiển thị PC có tên người dùng
- Has Local IP: Chỉ hiển thị PC có IP local
- Has Public IP: Chỉ hiển thị PC có IP public
- Recent: PC cập nhật trong 7 ngày qua
- Updated Today: PC cập nhật hôm nay

## Cách sử dụng

### 1. Thu thập dữ liệu từ client
Client có thể gửi request POST tới `/api/v1/pc-infos` để lưu thông tin PC.

### 2. Quản lý trong admin
Admin có thể truy cập Filament panel để xem và quản lý tất cả PC đã kết nối.

### 3. Tự động cập nhật
Khi cùng một PC kết nối lại, hệ thống sẽ tự động cập nhật thông tin thay vì tạo record mới.

## Validation Rules

- `host_name`: Max 255 ký tự
- `user_name`: Max 255 ký tự
- `password`: Max 255 ký tự
- `local_ip_address`: Phải là địa chỉ IP hợp lệ
- `public_ip_address`: Phải là địa chỉ IP hợp lệ

## Sample Data

Để tạo dữ liệu mẫu, chạy:
```bash
php artisan db:seed --class=PcInfoSeeder
```

Hoặc tạo factory data:
```bash
php artisan tinker
PcInfo::factory(50)->create()
```

## Security Notes

- Password field được ẩn trong API responses
- Chỉ admin mới có thể xem toàn bộ thông tin trong panel
- IP addresses được validate để đảm bảo format đúng


