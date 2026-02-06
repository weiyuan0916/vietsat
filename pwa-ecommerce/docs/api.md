# Vietnam Satellite API Documentation

## Overview

API base URL: `https://pwa-ecommerce.test/api/v1`

All API responses follow a standard format:

```json
{
    "success": true,
    "data": {...},
    "meta": {...},
    "links": {...}
}
```

---

## Services API

### List All Services

Get a paginated list of all active services.

**Endpoint:** `GET /services`

**Query Parameters:**

| Parameter | Type | Default | Max | Description |
|-----------|------|---------|-----|-------------|
| `page` | integer | 1 | - | Current page number |
| `per_page` | integer | 10 | 100 | Items per page |

**Example Request:**

```bash
curl -X GET "https://pwa-ecommerce.test/api/v1/services?page=1&per_page=10"
```

**Example Response:**

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "Default Plan",
            "duration_days": 90,
            "price": 100000,
            "formatted_price": "100,000 VND",
            "is_active": true,
            "created_at": "2026-01-30T08:38:42.000000Z",
            "updated_at": "2026-01-30T08:38:42.000000Z"
        },
        {
            "id": 2,
            "name": "VIP Package",
            "duration_days": 180,
            "price": 500000,
            "formatted_price": "500,000 VND",
            "is_active": true,
            "created_at": "2026-01-31T10:00:00.000000Z",
            "updated_at": "2026-01-31T10:00:00.000000Z"
        }
    ],
    "meta": {
        "current_page": 1,
        "last_page": 1,
        "per_page": 10,
        "total": 2,
        "from": 1,
        "to": 2
    },
    "links": {
        "first": "https://pwa-ecommerce.test/api/v1/services?page=1",
        "last": "https://pwa-ecommerce.test/api/v1/services?page=1",
        "prev": null,
        "next": null
    }
}
```

---

### Get Service Details

Get details of a specific service by ID.

**Endpoint:** `GET /services/{id}`

**URL Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `id` | integer | Service ID |

**Example Request:**

```bash
curl -X GET "https://pwa-ecommerce.test/api/v1/services/1"
```

**Example Response:**

```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "Default Plan",
        "duration_days": 90,
        "price": 100000,
        "formatted_price": "100,000 VND",
        "is_active": true,
        "created_at": "2026-01-30T08:38:42.000000Z",
        "updated_at": "2026-01-30T08:38:42.000000Z"
    }
}
```

**Error Response (404):**

```json
{
    "success": false,
    "message": "No query results for model [App\\Models\\Service]."
}
```

---

### Get Default Service

Get the default service plan (first active service).

**Endpoint:** `GET /services/default`

**Example Request:**

```bash
curl -X GET "https://pwa-ecommerce.test/api/v1/services/default"
```

**Example Response:**

```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "Default Plan",
        "duration_days": 90,
        "price": 100000,
        "formatted_price": "100,000 VND"
    }
}
```

---

## Orders API

### Create Order

Create a new service order.

**Endpoint:** `POST /orders`

**Request Body:**

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `facebook_profile_link` | string | Yes | Facebook profile URL (must contain facebook.com) |

**Example Request:**

```bash
curl -X POST "https://pwa-ecommerce.test/api/v1/orders" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "facebook_profile_link": "https://www.facebook.com/username"
  }'
```

**Example Response (201 Created):**

```json
{
    "success": true,
    "data": {
        "order_code": "ORD-ABC123XYZ",
        "service_id": 1,
        "amount": 100000,
        "formatted_amount": "100,000 VND",
        "status": "pending",
        "expires_at": "2026-02-02T12:30:00.000000Z",
        "qr_content": "bank:ORD-ABC123XYZ:100000",
        "transfer_content": "ORD-ABC123XYZ",
        "created_at": "2026-02-02T12:25:00.000000Z"
    }
}
```

**Validation Error (422):**

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

---

### Get Order Details

Get details of a specific order by order code.

**Endpoint:** `GET /orders/{orderCode}`

**URL Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `orderCode` | string | Order code (e.g., ORD-ABC123XYZ) |

**Example Request:**

```bash
curl -X GET "https://pwa-ecommerce.test/api/v1/orders/ORD-ABC123XYZ"
```

**Example Response:**

```json
{
    "success": true,
    "data": {
        "order_code": "ORD-ABC123XYZ",
        "service_id": 1,
        "service_name": "Default Plan",
        "amount": 100000,
        "formatted_amount": "100,000 VND",
        "status": "pending",
        "expires_at": "2026-02-02T12:30:00.000000Z",
        "paid_at": null,
        "created_at": "2026-02-02T12:25:00.000000Z"
    }
}
```

**Order Status Values:**

| Status | Description |
|--------|-------------|
| `pending` | Order created, waiting for payment |
| `paid` | Payment received, service activated |
| `expired` | Order expired (not paid within 5 minutes) |

---

## WebSocket Events (Laravel Reverb)

### Payment Status Events

Subscribe to private channel: `order.{orderCode}`

**Events:**

#### payment.pending

```json
{
    "event": "payment.pending",
    "data": {
        "order_code": "ORD-ABC123XYZ",
        "message": "Order created, waiting for payment"
    }
}
```

#### payment.success

```json
{
    "event": "payment.success",
    "data": {
        "order_code": "ORD-ABC123XYZ",
        "message": "Payment received, service activated",
        "activated_at": "2026-02-02T12:26:00.000000Z"
    }
}
```

#### payment.expired

```json
{
    "event": "payment.expired",
    "data": {
        "order_code": "ORD-ABC123XYZ",
        "message": "Order expired, please create a new order"
    }
}
```

**Frontend Subscription:**

```javascript
const reverb = new Reverb.Reverb({
    authEndpoint: 'https://pwa-ecommerce.test/broadcasting/auth',
});

const channel = reverb.channel(`order.ORD-ABC123XYZ`);

channel.on('payment.pending', (data) => {
    console.log('Payment pending:', data);
});

channel.on('payment.success', (data) => {
    console.log('Payment success:', data);
    alert('Service activated successfully!');
});

channel.on('payment.expired', (data) => {
    console.log('Payment expired:', data);
    alert('Order expired. Please create a new order.');
});
```

---

## Error Codes

| Code | Description |
|------|-------------|
| 400 | Bad Request |
| 401 | Unauthorized |
| 403 | Forbidden |
| 404 | Not Found |
| 422 | Validation Error |
| 500 | Internal Server Error |

---

## Rate Limiting

- Default: 60 requests per minute
- Authenticated: 120 requests per minute

---

## Versioning

This is API version **v1**. Future versions will be available at `/api/v2`, `/api/v3`, etc.

---

## Changelog

### v1.0.0 (2026-02-01)

- Initial API release
- Services endpoints
- Orders endpoints
- WebSocket events for real-time payment status







