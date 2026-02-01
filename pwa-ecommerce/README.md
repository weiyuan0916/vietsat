# Vietnam Satellite - Framework7 + Laravel 12 API Integration

## Table of Contents

- [Project Overview](#project-overview)
- [Architecture](#architecture)
- [Technology Stack](#technology-stack)
- [Database Structure](#database-structure)
- [API Endpoints](#api-endpoints)
- [Frontend (Framework7)](#frontend-framework7)
- [Real-time Features](#real-time-features)
- [Installation Guide](#installation-guide)
- [Usage Guide](#usage-guide)
- [Project Structure](#project-structure)
- [Extensibility Guide](#extensibility-guide)
- [Security Best Practices](#security-best-practices)

---

## Project Overview

Vietnam Satellite is a production-ready PWA (Progressive Web Application) built with **Framework7** on the frontend and **Laravel 12** on the backend. The system provides a service subscription platform with the following features:

- **Single Service Plan**: 3-month subscription at 100,000 VND
- **Dynamic UI**: Service details loaded from backend API (no hardcoded values)
- **Payment Processing**: QR code generation and real-time payment status tracking
- **Real-time Updates**: WebSocket support via Laravel Reverb
- **Security**: Backend as single source of truth, no frontend price trust

---

## Architecture

```
┌─────────────────────────────────────────────────────────────────────┐
│                         ARCHITECTURE                                 │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│   Browser                                                           │
│   ┌─────────────────────────────────────────────────────────────┐   │
│   │  Framework7 SPA (Served at /app)                            │   │
│   │  - index.html (static)                                      │   │
│   │  - app.js                                                   │   │
│   │  - Fetches API from Laravel                                 │   │
│   │  - WebSocket via Laravel Reverb                             │   │
│   └─────────────────────────────────────────────────────────────┘   │
│                              │                                       │
│                     GET/POST/PUT/DELETE                             │
│                              ▼                                       │
│   ┌─────────────────────────────────────────────────────────────┐   │
│   │  Laravel 12 Backend                                         │   │
│   │  - API-only (routes/api.php)                                │   │
│   │  - MySQL Database                                           │   │
│   │  - Laravel Reverb (WebSocket)                               │   │
│   │  - Filament Admin (optional)                                │   │
│   └─────────────────────────────────────────────────────────────┘   │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

---

## Technology Stack

### Backend
- **Framework**: Laravel 12 (PHP 8.3+)
- **Database**: MySQL 8.0+
- **WebSocket**: Laravel Reverb
- **API**: RESTful JSON API

### Frontend
- **Framework**: Framework7 v9 (PWA, SPA)
- **HTTP Client**: Fetch API
- **Real-time**: WebSocket via Reverb

---

## Database Structure

### Services Table

| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT UNSIGNED | Primary key (auto-increment) |
| name | VARCHAR(255) | Service plan name |
| duration_days | INT UNSIGNED | Duration in days (e.g., 90 for 3 months) |
| price | BIGINT UNSIGNED | Price in VND (no decimals) |
| is_active | BOOLEAN | Whether the plan is active |
| created_at | TIMESTAMP | Creation timestamp |
| updated_at | TIMESTAMP | Update timestamp |

### Orders Table

| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT UNSIGNED | Primary key (auto-increment) |
| order_code | VARCHAR(50) | Unique order code (e.g., ORD-ABC123XYZ) |
| service_id | BIGINT UNSIGNED | Foreign key to services table |
| amount | BIGINT UNSIGNED | Order amount in VND |
| status | VARCHAR(20) | pending/paid/expired |
| expires_at | TIMESTAMP | Order expiration time (5 minutes) |
| paid_at | TIMESTAMP | Payment confirmation time (nullable) |
| bank_txn_id | VARCHAR(100) | Bank transaction ID (nullable, unique) |
| created_at | TIMESTAMP | Creation timestamp |
| updated_at | TIMESTAMP | Update timestamp |

### Default Service (Created via Seeder)

| Field | Value |
|-------|-------|
| id | 1 |
| name | Default Plan |
| duration_days | 90 |
| price | 100000 |
| is_active | true |

---

## API Endpoints

### Service Endpoints

#### Get Default Service
```
GET /api/service/default
```

**Response:**
```json
{
  "id": 1,
  "name": "Default Plan",
  "duration_days": 90,
  "price": 100000
}
```

### Order Endpoints

#### Create Order
```
POST /api/orders
```

**Request Headers:**
```
Content-Type: application/json
Accept: application/json
```

**Request Body:**
```json
{
  "facebook_profile_link": "https://facebook.com/username"
}
```

**Response (201 Created):**
```json
{
  "order_code": "ORD-ABC123XYZ",
  "amount": 100000,
  "expires_at": "2026-02-01T10:05:00+00:00",
  "qr_content": "bank://payment?code=ORD-ABC123XYZ&amount=100000"
}
```

#### Get Order Status
```
GET /api/orders/{orderCode}
```

**Response:**
```json
{
  "order_code": "ORD-ABC123XYZ",
  "amount": 100000,
  "status": "pending",
  "expires_at": "2026-02-01T10:05:00+00:00",
  "paid_at": null
}
```

---

## Frontend (Framework7)

### Frontend Location
- **Path**: `/public/app/`
- **Entry Point**: `/public/app/index.html`
- **Static Assets**: `/public/app/css/`, `/public/app/js/`, `/public/app/img/`

### UI Components

The frontend dynamically loads:
- **Service Name**: From `/api/service/default`
- **Duration**: From `/api/service/default` (displayed in days)
- **Price**: From `/api/service/default` (formatted as VND)

### Order Flow

1. User visits service page
2. Frontend fetches service config from API
3. User enters Facebook profile link
4. User clicks "Thanh toán & kích hoạt"
5. Frontend submits order to `/api/orders`
6. Backend returns order with QR content
7. Frontend displays QR code and payment info
8. Frontend subscribes to WebSocket channel for real-time updates
9. Payment status updates in real-time

### Frontend API Integration

```javascript
// Fetch service configuration
async function fetchDefaultService() {
    const response = await fetch('/api/service/default');
    return response.json();
}

// Create order
async function createOrder(facebookProfileLink) {
    const response = await fetch('/api/orders', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ facebook_profile_link: facebookProfileLink }),
    });
    return response.json();
}

// Subscribe to order updates
function subscribeToOrder(orderCode) {
    const reverb = new Reverb.Reverb({
        authEndpoint: '/broadcasting/auth',
    });
    
    return reverb.channel(`order.${orderCode}`);
}
```

---

## Real-time Features

### WebSocket Events

| Event | Channel | Description |
|-------|---------|-------------|
| payment.pending | private-order.{orderCode} | Order created, awaiting payment |
| payment.success | private-order.{orderCode} | Payment confirmed, service activated |
| payment.expired | private-order.{orderCode} | Order expired (5 minutes) |

### Event Payloads

**payment.pending:**
```json
{
  "order_code": "ORD-ABC123XYZ",
  "message": "Vui lòng hoàn tất thanh toán trong 5 phút",
  "amount": 100000
}
```

**payment.success:**
```json
{
  "order_code": "ORD-ABC123XYZ",
  "message": "Thanh toán thành công! Dịch vụ đã được kích hoạt.",
  "paid_at": "2026-02-01T10:03:00+00:00"
}
```

**payment.expired:**
```json
{
  "order_code": "ORD-ABC123XYZ",
  "message": "Đơn hàng đã hết hạn. Vui lòng tạo đơn mới."
}
```

---

## Installation Guide

### Prerequisites

- PHP 8.3+
- Composer 2+
- Node.js 20+
- MySQL 8.0+
- Laravel Reverb server

### Step 1: Clone and Install Dependencies

```bash
# Navigate to project directory
cd /Users/adward/Herd/vietsat/pwa-ecommerce

# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### Step 2: Configure Environment

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

Update `.env` file:

```env
APP_NAME="Vietnam Satellite"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=vietsat
DB_USERNAME=root
DB_PASSWORD=

# Reverb Configuration
REVERB_APP_ID=your_app_id
REVERB_APP_KEY=your_app_key
REVERB_APP_SECRET=your_app_secret
REVERB_HOST=127.0.0.1
REVERB_PORT=8080

BROADCAST_DRIVER=reverb
```

### Step 3: Run Migrations and Seeders

```bash
# Run migrations
php artisan migrate

# Run seeders (creates default service)
php artisan db:seed --class=ServiceSeeder
```

### Step 4: Start Development Servers

```bash
# Start Laravel development server (in one terminal)
php artisan serve

# Start Reverb server (in another terminal)
php artisan reverb:start

# Build frontend assets (in another terminal)
npm run dev
```

### Step 5: Access the Application

- **Frontend (SPA)**: http://localhost:8000/app/
- **API Base**: http://localhost:8000/api/

---

## Usage Guide

### Testing the API

#### 1. Get Default Service
```bash
curl http://localhost:8000/api/service/default
```

#### 2. Create an Order
```bash
curl -X POST http://localhost:8000/api/orders \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"facebook_profile_link": "https://facebook.com/testuser"}'
```

#### 3. Check Order Status
```bash
curl http://localhost:8000/api/orders/ORD-XXXXXXXXXX
```

### Adding New Service Plans

1. Insert new record into `services` table:
```sql
INSERT INTO services (name, duration_days, price, is_active) 
VALUES ('Premium Plan', 180, 150000, true);
```

2. Frontend will automatically display the first active service

---

## Project Structure

```
pwa-ecommerce/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       └── Api/
│   │           ├── OrderController.php
│   │           └── ServiceController.php
│   ├── Models/
│   │   ├── Order.php
│   │   └── Service.php
│   └── Events/
│       ├── PaymentPending.php
│       ├── PaymentSuccess.php
│       └── PaymentExpired.php
├── bootstrap/
│   ├── app.php
│   └── providers.php
├── config/
│   ├── app.php
│   ├── database.php
│   ├── reverb.php
│   └── broadcasting.php
├── database/
│   ├── migrations/
│   │   ├── 2024_01_01_000001_create_services_table.php
│   │   └── 2024_01_01_000002_create_orders_table.php
│   └── seeders/
│       └── ServiceSeeder.php
├── public/
│   └── app/
│       ├── index.html
│       ├── css/
│       ├── js/
│       │   ├── app.js
│       │   └── framework7-bundle.js
│       ├── fonts/
│       ├── img/
│       └── manifest.json
├── routes/
│   └── api.php
├── storage/
│   ├── framework/
│   └── logs/
├── tests/
├── vendor/
├── composer.json
├── package.json
├── vite.config.js
└── artisan
```

---

## Extensibility Guide

### Adding Multiple Service Plans

The system is designed to support multiple service plans without code changes:

1. **Database**: Add new rows to `services` table
2. **Backend**: The `ServiceController::default()` returns the first active service
3. **Frontend**: Automatically displays whichever service is returned by the API

### Adding New Order Statuses

1. Add status constant to `Order` model:
```php
public const STATUS_CANCELLED = 'cancelled';
```

2. Create new event class following existing patterns

3. Update frontend to handle new event type

### Customizing QR Code Generation

Modify `OrderController::store()` to generate QR content based on your payment gateway requirements:

```php
// Example: VietQR integration
$qrContent = "https://vietqr.com/pay?bank=123&acc=456&amount={$order->amount}&memo={$order->order_code}";
```

---

## Security Best Practices

### ✅ Implemented Security Measures

1. **No Frontend Price Trust**: All prices come from backend
2. **Server-side Validation**: Input validation in Laravel controllers
3. **Database Transactions**: Payment processing uses DB transactions
4. **Unique Constraints**: `order_code` and `bank_txn_id` are unique
5. **Private Channels**: WebSocket channels are private and authenticated
6. **Order Expiration**: Orders expire after 5 minutes

### ⚠️ Important Notes

- **NEVER** trust client-side input for pricing
- **ALWAYS** validate and recalculate prices server-side
- **NEVER** confirm payment based on client callback alone
- **ALWAYS** use database transactions for payment operations
- **ENSURE** WebSocket channels are properly authenticated

---

## Maintenance

### Database Maintenance

```bash
# Check for pending migrations
php artisan migrate:status

# Run migrations
php artisan migrate

# Rollback last migration
php artisan migrate:rollback

# Refresh all migrations (WARNING: deletes data)
php artisan migrate:fresh
```

### Cache Clearing

```bash
# Clear application cache
php artisan cache:clear

# Clear route cache
php artisan route:clear

# Clear config cache
php artisan config:clear
```

### Queue Workers (if implemented)

```bash
# Start queue worker
php artisan queue:work
```

---

## Troubleshooting

### Common Issues

#### 1. Migration Errors
```
Solution: Check database connection in .env and ensure MySQL is running
```

#### 2. WebSocket Connection Failed
```
Solution: Verify Reverb server is running on configured port
```

#### 3. Frontend Not Loading Service Data
```
Solution: Check browser console for CORS errors and API accessibility
```

#### 4. QR Code Not Displaying
```
Solution: Verify qr_content is being generated in order response
```

### Logs Location

- **Laravel Logs**: `storage/logs/laravel.log`
- **Reverb Logs**: Check terminal output when running `php artisan reverb:start`

---

## License

This project is proprietary software. All rights reserved.

---

## Support

For technical support or questions, please contact the development team.

---

## Version History

| Version | Date | Changes |
|---------|------|---------|
| 1.0.0 | 2026-01-31 | Initial implementation |

---

**Built with ❤️ using Laravel 12 + Framework7**
