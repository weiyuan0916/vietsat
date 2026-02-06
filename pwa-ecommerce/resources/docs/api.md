# Vietnam Satellite API Documentation

## Tổng quan

API này cung cấp chức năng quản lý dịch vụ và đơn hàng cho hệ thống Vietnam Satellite, bao gồm:

- **Quản lý dịch vụ (Services)**: Lấy thông tin các gói dịch vụ
- **Quản lý đơn hàng (Orders)**: Tạo và theo dõi đơn hàng
- **Thanh toán realtime**: Nhận thông báo trạng thái thanh toán qua WebSocket

---

## Thông tin cơ bản

### Base URL

```
https://your-domain.com/api/v1
```

### Định dạng dữ liệu

- **Request**: `Content-Type: application/json`
- **Response**: `Content-Type: application/json`

### Mã hóa ký tự

Tất cả responses sử dụng UTF-8 encoding.

---

## Database Schema

### Bảng Services

| Trường | Kiểu dữ liệu | Mô tả |
|--------|--------------|-------|
| `id` | BIGINT | Primary key |
| `name` | VARCHAR(255) | Tên gói dịch vụ |
| `duration_days` | UNSIGNED INT | Số ngày sử dụng |
| `price` | UNSIGNED BIGINT | Giá tiền (VND, không có decimal) |
| `is_active` | BOOLEAN | Trạng thái hoạt động |
| `created_at` | TIMESTAMP | Thời gian tạo |
| `updated_at` | TIMESTAMP | Thời gian cập nhật |

### Bảng Service Orders

| Trường | Kiểu dữ liệu | Mô tả |
|--------|--------------|-------|
| `id` | BIGINT | Primary key |
| `order_code` | VARCHAR(50) | Mã đơn hàng (unique) |
| `service_id` | BIGINT | Foreign key đến services |
| `amount` | UNSIGNED BIGINT | Số tiền (VND) |
| `status` | VARCHAR(20) | Trạng thái: `pending`, `paid`, `expired` |
| `expires_at` | TIMESTAMP | Thời hạn thanh toán |
| `paid_at` | TIMESTAMP | Thời gian thanh toán thành công (nullable) |
| `bank_txn_id` | VARCHAR(100) | Mã giao dịch ngân hàng (nullable, unique) |
| `facebook_profile_link` | VARCHAR(255) | Link profile Facebook (nullable) |
| `created_at` | TIMESTAMP | Thời gian tạo |
| `updated_at` | TIMESTAMP | Thời gian cập nhật |

---

## Services API

### 1. Lấy danh sách tất cả dịch vụ

```http
GET /api/v1/services
```

#### Mô tả

Trả về danh sách tất cả các gói dịch vụ đang hoạt động với phân trang.

#### Query Parameters

| Tham số | Kiểu | Mặc định | Mô tả |
|---------|------|----------|-------|
| `page` | integer | 1 | Số trang hiện tại |
| `per_page` | integer | 10 | Số items mỗi trang (tối đa: 100) |

#### Response thành công (200)

```json
{
  "status": true,
  "message": "Lấy danh sách dịch vụ thành công.",
  "data": [
    {
      "id": 1,
      "name": "Default Plan",
      "duration_days": 90,
      "price": 100000,
      "formatted_price": "100,000 VND",
      "is_active": true,
      "created_at": "2026-01-30T10:00:00Z",
      "updated_at": "2026-01-30T10:00:00Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 10,
    "total": 1,
    "from": 1,
    "to": 1
  },
  "links": {
    "first": "https://your-domain.com/api/v1/services?page=1",
    "last": "https://your-domain.com/api/v1/services?page=1",
    "prev": null,
    "next": null
  }
}
```

#### Ví dụ cURL

```bash
curl -X GET "https://your-domain.com/api/v1/services?page=1&per_page=10" \
  -H "Accept: application/json"
```

---

### 2. Lấy gói dịch vụ mặc định

```http
GET /api/v1/services/default
```

#### Mô tả

Trả về thông tin gói dịch vụ mặc định (gói đầu tiên và đang hoạt động). Đây là endpoint quan trọng mà **Frontend phải sử dụng** để lấy thông tin giá và thời hạn.

> ⚠️ **Quy tắc quan trọng**: Frontend KHÔNG được hardcode giá hoặc thời hạn. Luôn fetch từ endpoint này.

#### Response thành công (200)

```json
{
  "status": true,
  "message": "Lấy thông tin dịch vụ thành công.",
  "data": {
    "id": 1,
    "name": "Default Plan",
    "duration_days": 90,
    "price": 100000,
    "formatted_price": "100,000 VND"
  }
}
```

#### Response lỗi (404)

```json
{
  "status": false,
  "message": "Không tìm thấy dịch vụ hoạt động.",
  "data": null
}
```

#### Ví dụ sử dụng

```javascript
// JavaScript/TypeScript
async function getDefaultService() {
  const response = await fetch('/api/v1/services/default');
  const data = await response.json();
  
  if (data.success) {
    const service = data.data;
    console.log(`Gói: ${service.name}`);
    console.log(`Giá: ${service.formatted_price}`);
    console.log(`Thời hạn: ${service.duration_days} ngày`);
    return service;
  }
}
```

```php
// PHP/Laravel
$response = Http::get('https://your-domain.com/api/v1/services/default');

if ($response->json('success')) {
    $service = $response->json('data');
    echo "Gói: {$service['name']}\n";
    echo "Giá: {$service['formatted_price']}\n";
    echo "Thời hạn: {$service['duration_days']} ngày";
}
```

---

### 3. Lấy thông tin dịch vụ theo ID

```http
GET /api/v1/services/{id}
```

#### Mô tả

Trả về thông tin chi tiết của một dịch vụ cụ thể.

#### URL Parameters

| Tham số | Kiểu | Mô tả |
|---------|------|-------|
| `id` | integer | ID của dịch vụ |

#### Response thành công (200)

```json
{
  "status": true,
  "message": "Lấy thông tin dịch vụ thành công.",
  "data": {
    "id": 1,
    "name": "Default Plan",
    "duration_days": 90,
    "price": 100000,
    "formatted_price": "100,000 VND",
    "is_active": true,
    "created_at": "2026-01-30T10:00:00Z",
    "updated_at": "2026-01-30T10:00:00Z"
  }
}
```

#### Response lỗi (404)

```json
{
  "status": false,
  "message": "Không tìm thấy dịch vụ.",
  "data": null
}
```

---

## Orders API

### 1. Tạo đơn hàng mới

```http
POST /api/v1/orders
```

#### Mô tả

Tạo một đơn hàng mới cho dịch vụ. Endpoint này sẽ:

1. Lấy gói dịch vụ mặc định
2. Tạo mã đơn hàng duy nhất
3. Thiết lập thời hạn thanh toán (5 phút)
4. Sinh nội dung QR code

#### Request Body

```json
{
  "facebook_profile_link": "https://facebook.com/username"
}
```

| Tham số | Kiểu | Bắt buộc | Mô tả |
|---------|------|----------|-------|
| `facebook_profile_link` | string | Có | Link profile Facebook hợp lệ |

#### Validation Rules

- Phải là URL hợp lệ
- Phải chứa `facebook.com` trong domain

#### Response thành công (201)

```json
{
  "order_code": "ORD-ABC123XYZ9",
  "amount": 100000,
  "expires_at": "2026-01-31T10:05:00Z",
  "qr_content": "bank:ORD-ABC123XYZ9:100000",
  "status": "pending",
  "service": {
    "id": 1,
    "name": "Default Plan",
    "duration_days": 90
  }
}
```

| Trường | Mô tả |
|--------|-------|
| `order_code` | Mã đơn hàng duy nhất (sử dụng để tra cứu và WebSocket channel) |
| `amount` | Số tiền thanh toán (VND) - **tính từ backend** |
| `expires_at` | Thời hạn thanh toán (ISO 8601) |
| `qr_content` | Nội dung QR code cho app ngân hàng |
| `status` | Trạng thái đơn hàng |

#### Response lỗi (422)

```json
{
  "success": false,
  "message": "The facebook profile link field is required.",
  "errors": {
    "facebook_profile_link": [
      "The facebook profile link field is required."
    ]
  }
}
```

```json
{
  "success": false,
  "message": "The facebook profile link must be a valid URL.",
  "errors": {
    "facebook_profile_link": [
      "The facebook profile link must be a valid URL."
    ]
  }
}
```

#### Response lỗi (404)

```json
{
  "success": false,
  "message": "No active service found. Please contact support.",
  "error": "SERVICE_NOT_FOUND"
}
```

#### Ví dụ tạo đơn hàng

```javascript
// JavaScript/TypeScript
async function createOrder(facebookProfileLink) {
  const response = await fetch('/api/v1/orders', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json'
    },
    body: JSON.stringify({ facebook_profile_link: facebookProfileLink })
  });
  
  const data = await response.json();
  
  if (data.order_code) {
    console.log('Đơn hàng đã được tạo:');
    console.log(`Mã: ${data.order_code}`);
    console.log(`Số tiền: ${data.amount} VND`);
    console.log(`Hạn: ${data.expires_at}`);
    console.log(`QR: ${data.qr_content}`);
    
    // Subscribe WebSocket channel
    subscribeToOrderChannel(data.order_code);
  }
  
  return data;
}

// Sử dụng
createOrder('https://facebook.com/username');
```

```php
// PHP/Laravel
$response = Http::post('https://your-domain.com/api/v1/orders', [
    'facebook_profile_link' => 'https://facebook.com/username'
]);

if ($response->successful()) {
    $order = $response->json();
    echo "Mã đơn hàng: {$order['order_code']}\n";
    echo "Số tiền: {$order['amount']} VND\n";
    echo "QR Content: {$order['qr_content']}\n";
}
```

---

### 2. Lấy thông tin đơn hàng

```http
GET /api/v1/orders/{orderCode}
```

#### Mô tả

Trả về thông tin chi tiết và trạng thái của một đơn hàng.

#### URL Parameters

| Tham số | Kiểu | Mô tả |
|---------|------|-------|
| `orderCode` | string | Mã đơn hàng (ORD-XXXXX) |

#### Response thành công (200)

```json
{
  "order_code": "ORD-ABC123XYZ9",
  "amount": 100000,
  "status": "pending",
  "expires_at": "2026-01-31T10:05:00Z",
  "paid_at": null,
  "created_at": "2026-01-31T10:00:00Z",
  "service": {
    "id": 1,
    "name": "Default Plan",
    "duration_days": 90
  }
}
```

#### Response thành công - Đã thanh toán (200)

```json
{
  "order_code": "ORD-ABC123XYZ9",
  "amount": 100000,
  "status": "paid",
  "expires_at": "2026-01-31T10:05:00Z",
  "paid_at": "2026-01-31T10:02:30Z",
  "created_at": "2026-01-31T10:00:00Z",
  "service": {
    "id": 1,
    "name": "Default Plan",
    "duration_days": 90
  }
}
```

#### Response lỗi (404)

```json
{
  "success": false,
  "message": "Order not found.",
  "error": "ORDER_NOT_FOUND"
}
```

#### Ví dụ tra cứu đơn hàng

```javascript
async function getOrderStatus(orderCode) {
  const response = await fetch(`/api/v1/orders/${orderCode}`);
  const data = await response.json();
  
  if (data.status) {
    switch (data.status) {
      case 'pending':
        console.log('⏳ Đang chờ thanh toán');
        break;
      case 'paid':
        console.log('✅ Đã thanh toán thành công!');
        break;
      case 'expired':
        console.log('❌ Đã hết hạn');
        break;
    }
  }
  
  return data;
}
```

---

## Realtime Events (WebSocket)

### Tổng quan

Hệ thống sử dụng **Laravel Reverb** để gửi thông báo realtime về trạng thanh toán. Sau khi tạo đơn hàng, frontend cần subscribe vào private channel để nhận cập nhật.

### Channel

```
private-order.{orderCode}
```

### Xác thực

Private channel yêu cầu xác thực. Sử dụng Laravel Sanctum hoặc session auth.

### Events

#### 1. PaymentPending

Gửi khi đơn hàng được tạo thành công và đang chờ thanh toán.

```javascript
// Laravel Reverb event
{
  "event": "PaymentPending",
  "data": {
    "order_code": "ORD-ABC123XYZ9",
    "amount": 100000,
    "status": "pending",
    "expires_at": "2026-01-31T10:05:00Z",
    "message": "Đơn hàng đã được tạo. Vui lòng thanh toán trong 5 phút."
  }
}
```

#### 2. PaymentSuccess

Gửi khi thanh toán thành công.

```javascript
// Laravel Reverb event
{
  "event": "PaymentSuccess",
  "data": {
    "order_code": "ORD-ABC123XYZ9",
    "amount": 100000,
    "status": "paid",
    "paid_at": "2026-01-31T10:02:30Z",
    "bank_txn_id": "TXN123456789",
    "message": "Thanh toán thành công! Dịch vụ đã được kích hoạt."
  }
}
```

#### 3. PaymentExpired

Gửi khi đơn hàng hết hạn chưa thanh toán.

```javascript
// Laravel Reverb event
{
  "event": "PaymentExpired",
  "data": {
    "order_code": "ORD-ABC123XYZ9",
    "amount": 100000,
    "status": "expired",
    "expires_at": "2026-01-31T10:05:00Z",
    "message": "Đơn hàng đã hết hạn. Vui lòng tạo đơn hàng mới."
  }
}
```

### Ví dụ tích hợp WebSocket

```javascript
// JavaScript - Sử dụng Laravel Echo
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

const echo = new Echo({
  broadcaster: 'reverb',
  key: 'your-reverb-key',
  wsHost: 'your-domain.com',
  wsPort: 6001,
  forceTLS: true,
  authEndpoint: '/broadcasting/auth',
  auth: {
    headers: {
      'Authorization': 'Bearer ' + accessToken
    }
  }
});

function subscribeToOrderChannel(orderCode) {
  const channel = echo.private(`order.${orderCode}`);
  
  channel.listen('.PaymentPending', (e) => {
    console.log('Payment pending:', e.data);
    updateUI(e.data);
  });
  
  channel.listen('.PaymentSuccess', (e) => {
    console.log('Payment success:', e.data);
    updateUI(e.data);
    showSuccessMessage(e.data.message);
  });
  
  channel.listen('.PaymentExpired', (e) => {
    console.log('Payment expired:', e.data);
    updateUI(e.data);
    showExpiredMessage(e.data.message);
  });
  
  channel.subscribed(() => {
    console.log(`Subscribed to order.${orderCode}`);
  });
  
  channel.error((error) => {
    console.error('Channel error:', error);
  });
}

// Hủy subscription khi không cần
function unsubscribeFromOrderChannel(orderCode) {
  echo.leave(`order.${orderCode}`);
}
```

---

## Mã lỗi

| Mã lỗi | HTTP Status | Mô tả |
|--------|-------------|-------|
| `SERVICE_NOT_FOUND` | 404 | Không tìm thấy dịch vụ hoạt động |
| `ORDER_NOT_FOUND` | 404 | Không tìm thấy đơn hàng |
| `VALIDATION_ERROR` | 422 | Dữ liệu đầu vào không hợp lệ |
| `ORDER_EXPIRED` | 400 | Đơn hàng đã hết hạn |
| `ALREADY_PAID` | 400 | Đơn hàng đã được thanh toán |

---

## Response Structure

### Success Response

```json
{
  "success": true,
  "data": { ... }
}
```

### Error Response

```json
{
  "success": false,
  "message": "Mô tả lỗi",
  "error": "ERROR_CODE",
  "errors": { ... } // Optional - validation errors
}
```

---

## Quy tắc bảo mật

### Backend là nguồn sự thật duy nhất

1. **KHÔNG hardcode giá** - Luôn fetch từ `/api/v1/services/default`
2. **KHÔNG hardcode thời hạn** - Luôn lấy từ backend
3. **KHÔNG tính toán expiration** - Backend tính và trả về
4. **KHÔNG trust order amount từ frontend** - Backend tính từ service price

### Quy trình thanh toán an toàn

```
1. Frontend: GET /api/v1/services/default → Lấy price, duration
2. Frontend: POST /api/v1/orders → Tạo đơn, backend trả về order_code + amount
3. Frontend: Subscribe WebSocket channel
4. Backend: Phát event khi có thay đổi trạng thái
5. Frontend: Nhận event và cập nhật UI
```

---

## Hướng dẫn tích hợp Frontend

### Workflow đầy đủ

```javascript
class PaymentService {
  constructor() {
    this.echo = null;
    this.currentOrderCode = null;
  }
  
  // Bước 1: Lấy thông tin dịch vụ
  async getServiceInfo() {
    const response = await fetch('/api/v1/services/default');
    const data = await response.json();
    
    if (data.success) {
      return data.data; // { id, name, price, duration_days, formatted_price }
    }
    throw new Error('Không thể lấy thông tin dịch vụ');
  }
  
  // Bước 2: Tạo đơn hàng
  async createOrder(facebookProfileLink) {
    const response = await fetch('/api/v1/orders', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({ facebook_profile_link: facebookProfileLink })
    });
    
    const data = await response.json();
    
    if (data.order_code) {
      this.currentOrderCode = data.order_code;
      this.subscribeToPaymentStatus(data.order_code);
    }
    
    return data;
  }
  
  // Bước 3: Subscribe WebSocket
  subscribeToPaymentStatus(orderCode) {
    const channel = echo.private(`order.${orderCode}`);
    
    channel.listen('.PaymentPending', (e) => {
      this.onPaymentPending(e.data);
    });
    
    channel.listen('.PaymentSuccess', (e) => {
      this.onPaymentSuccess(e.data);
    });
    
    channel.listen('.PaymentExpired', (e) => {
      this.onPaymentExpired(e.data);
    });
  }
  
  // Bước 4: Kiểm tra trạng thái thủ công
  async checkOrderStatus(orderCode) {
    const response = await fetch(`/api/v1/orders/${orderCode}`);
    return response.json();
  }
  
  // Event handlers
  onPaymentPending(data) {
    // Hiển thị QR code
    // Bắt đầu đếm ngược
  }
  
  onPaymentSuccess(data) {
    // Hiển thị thông báo thành công
    // Kích hoạt dịch vụ
  }
  
  onPaymentExpired(data) {
    // Hiển thị thông báo hết hạn
    // Cho phép tạo đơn mới
  }
}

// Sử dụng
const paymentService = new PaymentService();

// 1. Hiển thị thông tin dịch vụ
const service = await paymentService.getServiceInfo();
document.getElementById('service-name').textContent = service.name;
document.getElementById('service-price').textContent = service.formatted_price;
document.getElementById('service-duration').textContent = `${service.duration_days} ngày`;

// 2. Xử lý submit form
document.getElementById('payment-form').addEventListener('submit', async (e) => {
  e.preventDefault();
  const fbLink = document.getElementById('facebook-link').value;
  await paymentService.createOrder(fbLink);
});
```

---

## Testing

### Curl Examples

```bash
# Lấy dịch vụ mặc định
curl -X GET "https://your-domain.com/api/v1/services/default" \
  -H "Accept: application/json"

# Tạo đơn hàng
curl -X POST "https://your-domain.com/api/v1/orders" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"facebook_profile_link": "https://facebook.com/username"}'

# Kiểm tra trạng thái đơn hàng
curl -X GET "https://your-domain.com/api/v1/orders/ORD-ABC123XYZ9" \
  -H "Accept: application/json"
```

### PHPUnit Tests

```php
// tests/Feature/ServiceApiTest.php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ServiceApiTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function it_can_get_default_service()
    {
        Service::factory()->create([
            'name' => 'Default Plan',
            'price' => 100000,
            'duration_days' => 90,
            'is_active' => true,
        ]);
        
        $response = $this->getJson('/api/v1/services/default');
        
        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'name',
                    'price',
                    'duration_days',
                    'formatted_price',
                ],
            ])
            ->assertJsonPath('data.name', 'Default Plan')
            ->assertJsonPath('data.price', 100000);
    }
    
    /** @test */
    public function it_can_create_order()
    {
        Service::factory()->create([
            'name' => 'Default Plan',
            'price' => 100000,
            'duration_days' => 90,
            'is_active' => true,
        ]);
        
        $response = $this->postJson('/api/v1/orders', [
            'facebook_profile_link' => 'https://facebook.com/testuser',
        ]);
        
        $response->assertCreated()
            ->assertJsonStructure([
                'order_code',
                'amount',
                'expires_at',
                'qr_content',
                'status',
            ])
            ->assertJsonPath('amount', 100000)
            ->assertJsonPath('status', 'pending');
    }
}
```

---

## Version History

| Version | Date | Changes |
|---------|------|---------|
| 1.0.0 | 2026-01-30 | Initial release |

---

## Hỗ trợ

Liên hệ đội ngũ hỗ trợ qua email hoặc các kênh sau:
- Email: support@vietsat.com
- Website: https://vietsat.com

