# Vietnam Satellite - Frontend

Framework7 PWA Frontend kết nối Laravel Backend API.

## Cấu trúc

```
frontend/
├── js/
│   ├── config.js           # Cấu hình ứng dụng
│   ├── api/                # API Service Layer
│   │   ├── base.js        # ApiService class
│   │   ├── service.js     # Service API
│   │   ├── order.js       # Order API
│   │   └── index.js       # Entry point
│   ├── stores/             # State Management
│   │   ├── service.js     # Service Store
│   │   ├── order.js       # Order Store
│   │   └── index.js       # Entry point
│   └── app.js             # Main app entry
├── pages/                 # Framework7 pages
├── components/            # Reusable components
├── css/                   # Stylesheets
├── index.html            # Main HTML entry
├── vite.config.js        # Vite configuration
└── package.json          # Dependencies
```

## Cách chạy

### Option 1: Static Files (Hiện tại)

Chạy trực tiếp với web server:

```bash
# Sử dụng PHP built-in server
php -S localhost:8000 -t public

# Hoặc sử dụng Laravel
php artisan serve
```

### Option 2: Mobile Apps (iOS/Android)

Sử dụng Capacitor để build thành ứng dụng di động:

```bash
cd frontend

# Cài đặt dependencies
npm install

# Build web assets trước
npm run build

# Sync với native projects
npm run sync

# Chạy trên iOS
npm run ios

# Hoặc chạy trên Android
npm run android

# Build release
npm run ios:build
npm run android:build
```

#### Yêu cầu

- **iOS**: Xcode 15+, CocoaPods
- **Android**: Android Studio, JDK 17+

#### Cấu hình

Chỉnh sửa `capacitor.config.json` để thay đổi:
- App ID
- App Name
- Icon, splash screen

## Cấu hình API

### Cách 1: Qua Backend (Khuyến nghị)

Backend truyền config qua `window.APP_CONFIG`:

```php
// Laravel Blade template
<script>
    window.APP_CONFIG = {
        apiBaseUrl: '{{ config('app.api_url') }}'
    };
</script>
```

### Cách 2: Qua Environment Variable

Tạo file `.env`:

```bash
API_BASE_URL=https://your-api-domain.com/api
```

## API Endpoints

| Method | Endpoint | Mô tả |
|--------|----------|--------|
| GET | `/api/service/default` | Lấy thông tin dịch vụ mặc định |
| POST | `/api/orders` | Tạo order mới |
| GET | `/api/orders/{order_code}` | Kiểm tra trạng thái order |

## Development

### Thêm API Module Mới

1. Tạo file mới trong `js/api/`:

```javascript
// js/api/payment.js
(function() {
  'use strict';

  const PaymentApi = {
    async getHistory(params) {
      const api = new window.ApiService(window.AppConfig.apiBaseUrl);
      return await api.get('/payments', params);
    }
  };

  window.PaymentApi = PaymentApi;
})();
```

2. Thêm script vào `index.html`:

```html
<script src="/app/js/api/payment.js"></script>
```

### Thêm Store Mới

```javascript
// js/stores/user.js
class UserStore {
  constructor() {
    this._user = null;
    this._subscribers = [];
  }
  // ...
}
window.UserStore = new UserStore();
```

## Realtime Payment

Sử dụng Laravel Echo + Reverb để nhận thông báo payment real-time:

```javascript
// Subscribe to order updates
const channel = Echo.private('order.' + orderCode);

channel.listen('payment.success', (data) => {
    console.log('Payment confirmed!', data);
});
```

## License

MIT
