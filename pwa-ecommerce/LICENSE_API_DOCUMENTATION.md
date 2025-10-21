# License Management API Documentation

## Overview
This API provides comprehensive software license management functionality including activation, validation, renewal, and machine tracking.

## Base URL
```
http://your-domain.com/api/v1
```

## Database Schema

### Licenses Table
```sql
- id: bigint (primary key)
- license_key: string (unique)
- type: enum (trial, standard, premium, enterprise)
- status: enum (active, expired, suspended, revoked)
- max_activations: integer (default: 1)
- current_activations: integer (default: 0)
- issued_at: timestamp
- expires_at: timestamp
- last_renewed_at: timestamp (nullable)
- metadata: json (nullable)
- created_at, updated_at, deleted_at
```

### License Activations Table
```sql
- id: bigint (primary key)
- license_id: foreign key
- machine_id: string (unique machine identifier)
- machine_name: string (nullable)
- ip_address: string (nullable)
- hardware_info: json (nullable)
- status: enum (active, deactivated, suspended)
- activated_at: timestamp
- last_validated_at: timestamp (nullable)
- deactivated_at: timestamp (nullable)
- created_at, updated_at, deleted_at
```

## API Endpoints

### 1. Activate License

**Endpoint:** `POST /api/v1/licenses/activate`

**Description:** Activates a license key on a specific machine. Stores machine information for tracking.

**Request Body:**
```json
{
  "license_key": "LS-XXXX-XXXX-XXXX-XXXX",
  "machine_id": "unique-machine-identifier",
  "machine_name": "My Computer",
  "ip_address": "192.168.1.1",
  "hardware_info": {
    "cpu": "Intel i7-9700K",
    "ram": "16GB",
    "os": "Windows 11 Pro",
    "disk": "512GB SSD"
  }
}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "License activated successfully.",
  "data": {
    "license": {
      "id": 1,
      "license_key": "LS-XXXX-XXXX-XXXX-XXXX",
      "type": "standard",
      "status": "active",
      "max_activations": 3,
      "current_activations": 1,
      "issued_at": "2025-01-01 00:00:00",
      "expires_at": "2026-01-01 00:00:00",
      "is_valid": true,
      "is_expired": false,
      "days_remaining": 365
    },
    "activation": {
      "id": 1,
      "machine_id": "unique-machine-identifier",
      "machine_name": "My Computer",
      "activated_at": "2025-10-21 10:00:00"
    }
  }
}
```

**Error Response (400):**
```json
{
  "success": false,
  "message": "License has expired.",
  "error": "ACTIVATION_FAILED"
}
```

---

### 2. Validate License

**Endpoint:** `POST /api/v1/licenses/validate`

**Description:** Validates if a license is still active and not expired for a specific machine. Updates last validation timestamp.

**Request Body:**
```json
{
  "license_key": "LS-XXXX-XXXX-XXXX-XXXX",
  "machine_id": "unique-machine-identifier"
}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "License is valid.",
  "data": {
    "valid": true,
    "license": {
      "id": 1,
      "license_key": "LS-XXXX-XXXX-XXXX-XXXX",
      "status": "active",
      "is_valid": true,
      "is_expired": false
    },
    "days_remaining": 365,
    "expires_at": "2026-01-01 00:00:00"
  }
}
```

**Error Response (400):**
```json
{
  "success": false,
  "message": "License has expired on 2025-10-20",
  "error": "VALIDATION_FAILED",
  "data": {
    "valid": false
  }
}
```

---

### 3. Check Status (Lightweight)

**Endpoint:** `POST /api/v1/licenses/check-status`

**Description:** Lightweight version of validate. Returns minimal data for quick checks.

**Request Body:**
```json
{
  "license_key": "LS-XXXX-XXXX-XXXX-XXXX",
  "machine_id": "unique-machine-identifier"
}
```

**Success Response (200):**
```json
{
  "success": true,
  "valid": true,
  "days_remaining": 365,
  "expires_at": "2026-01-01 00:00:00"
}
```

---

### 4. Renew License

**Endpoint:** `POST /api/v1/licenses/renew`

**Description:** Extends the expiration date of a license by specified number of days.

**Request Body:**
```json
{
  "license_key": "LS-XXXX-XXXX-XXXX-XXXX",
  "days": 365
}
```

**Note:** If `days` is not provided, defaults to 365 days.

**Success Response (200):**
```json
{
  "success": true,
  "message": "License renewed successfully.",
  "data": {
    "license": {
      "id": 1,
      "license_key": "LS-XXXX-XXXX-XXXX-XXXX",
      "status": "active",
      "expires_at": "2027-01-01 00:00:00",
      "last_renewed_at": "2025-10-21 10:00:00"
    },
    "new_expiration": "2027-01-01 00:00:00",
    "days_added": 365
  }
}
```

**Error Response (400):**
```json
{
  "success": false,
  "message": "License key not found.",
  "error": "RENEWAL_FAILED"
}
```

---

### 5. Deactivate License

**Endpoint:** `POST /api/v1/licenses/deactivate`

**Description:** Deactivates a license from a specific machine. Decrements activation count.

**Request Body:**
```json
{
  "license_key": "LS-XXXX-XXXX-XXXX-XXXX",
  "machine_id": "unique-machine-identifier"
}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "License deactivated successfully.",
  "data": {
    "license": {
      "id": 1,
      "license_key": "LS-XXXX-XXXX-XXXX-XXXX",
      "current_activations": 0
    }
  }
}
```

**Error Response (400):**
```json
{
  "success": false,
  "message": "No activation found for this machine.",
  "error": "DEACTIVATION_FAILED"
}
```

---

### 6. Get License Information

**Endpoint:** `GET /api/v1/licenses/{licenseKey}`

**Description:** Retrieves detailed information about a license including all activations.

**Success Response (200):**
```json
{
  "success": true,
  "data": {
    "license": {
      "id": 1,
      "license_key": "LS-XXXX-XXXX-XXXX-XXXX",
      "type": "standard",
      "status": "active",
      "max_activations": 3,
      "current_activations": 2,
      "issued_at": "2025-01-01 00:00:00",
      "expires_at": "2026-01-01 00:00:00",
      "is_valid": true,
      "is_expired": false,
      "days_remaining": 365,
      "activations": [
        {
          "id": 1,
          "machine_id": "machine-1",
          "machine_name": "Computer 1",
          "status": "active",
          "activated_at": "2025-10-21 10:00:00"
        },
        {
          "id": 2,
          "machine_id": "machine-2",
          "machine_name": "Computer 2",
          "status": "active",
          "activated_at": "2025-10-21 11:00:00"
        }
      ]
    },
    "is_valid": true,
    "is_expired": false,
    "days_remaining": 365
  }
}
```

**Error Response (404):**
```json
{
  "success": false,
  "message": "License key not found.",
  "error": "LICENSE_NOT_FOUND"
}
```

---

## Usage Examples

### PHP / Laravel HTTP Client
```php
use Illuminate\Support\Facades\Http;

// Activate License
$response = Http::post('https://your-domain.com/api/v1/licenses/activate', [
    'license_key' => 'LS-XXXX-XXXX-XXXX-XXXX',
    'machine_id' => 'unique-machine-id',
    'machine_name' => 'My Computer',
    'hardware_info' => [
        'cpu' => 'Intel i7',
        'ram' => '16GB',
        'os' => 'Windows 11'
    ]
]);

// Validate License
$response = Http::post('https://your-domain.com/api/v1/licenses/validate', [
    'license_key' => 'LS-XXXX-XXXX-XXXX-XXXX',
    'machine_id' => 'unique-machine-id'
]);

if ($response->json('data.valid')) {
    // License is valid
}
```

### cURL
```bash
# Activate License
curl -X POST https://your-domain.com/api/v1/licenses/activate \
  -H "Content-Type: application/json" \
  -d '{
    "license_key": "LS-XXXX-XXXX-XXXX-XXXX",
    "machine_id": "unique-machine-id",
    "machine_name": "My Computer"
  }'

# Validate License
curl -X POST https://your-domain.com/api/v1/licenses/validate \
  -H "Content-Type: application/json" \
  -d '{
    "license_key": "LS-XXXX-XXXX-XXXX-XXXX",
    "machine_id": "unique-machine-id"
  }'
```

### JavaScript / Axios
```javascript
// Activate License
const activateResponse = await axios.post('https://your-domain.com/api/v1/licenses/activate', {
  license_key: 'LS-XXXX-XXXX-XXXX-XXXX',
  machine_id: 'unique-machine-id',
  machine_name: 'My Computer',
  hardware_info: {
    cpu: 'Intel i7',
    ram: '16GB',
    os: 'Windows 11'
  }
});

// Validate License
const validateResponse = await axios.post('https://your-domain.com/api/v1/licenses/validate', {
  license_key: 'LS-XXXX-XXXX-XXXX-XXXX',
  machine_id: 'unique-machine-id'
});

if (validateResponse.data.data.valid) {
  console.log('License is valid');
}
```

---

## Error Codes

| Error Code | Description |
|------------|-------------|
| `ACTIVATION_FAILED` | License activation failed |
| `VALIDATION_FAILED` | License validation failed |
| `RENEWAL_FAILED` | License renewal failed |
| `DEACTIVATION_FAILED` | License deactivation failed |
| `LICENSE_NOT_FOUND` | License key not found |

---

## Machine ID Generation

The `machine_id` should be a unique identifier for each machine. Here are some recommendations:

### Windows (PowerShell)
```powershell
$machineId = Get-WmiObject Win32_ComputerSystemProduct | Select-Object -ExpandProperty UUID
```

### macOS / Linux
```bash
# Using system UUID
machine_id=$(cat /sys/class/dmi/id/product_uuid)

# Or using hostname and MAC address
machine_id=$(echo "$(hostname)-$(ifconfig | grep ether | head -n 1 | awk '{print $2}')" | md5sum | awk '{print $1}')
```

### PHP
```php
function getMachineId(): string
{
    if (PHP_OS_FAMILY === 'Windows') {
        exec('wmic csproduct get uuid', $output);
        return trim($output[1] ?? '');
    } else {
        exec('cat /sys/class/dmi/id/product_uuid 2>/dev/null || echo "$(hostname)-$(ifconfig | grep ether | head -n 1 | awk \'{print $2}\')" | md5sum | awk \'{print $1}\'', $output);
        return trim($output[0] ?? '');
    }
}
```

---

## Security Recommendations

1. **Use HTTPS** - Always use HTTPS in production to encrypt data in transit
2. **Rate Limiting** - Implement rate limiting to prevent abuse
3. **API Authentication** - Consider adding API authentication (Bearer tokens)
4. **IP Whitelisting** - Optionally whitelist trusted IPs
5. **Logging** - Log all activation/deactivation attempts for auditing
6. **Machine ID Validation** - Validate machine_id format on the client side

---

## Testing

Run migrations:
```bash
php artisan migrate
```

Create a test license via Tinker:
```bash
php artisan tinker
```

```php
use App\Services\LicenseService;
use App\Models\License;

$service = app(LicenseService::class);

// Create a new license
$license = $service->createLicense([
    'type' => 'standard',
    'max_activations' => 3,
    'status' => 'active'
]);

echo $license->license_key;
```

Test the API endpoints using Postman or curl with the generated license key.

---

## Architecture

The system follows Laravel best practices:

- **Models**: `License`, `LicenseActivation` - Eloquent models with relationships
- **Repositories**: Interface-based repositories for database abstraction
- **Services**: `LicenseService` - Business logic layer
- **Controllers**: `LicenseController` - Handle HTTP requests/responses
- **Resources**: `LicenseResource`, `LicenseActivationResource` - API response transformers
- **Requests**: Form request validators for input validation
- **Migrations**: Database schema definitions

---

## Support

For questions or issues, please contact your system administrator.

