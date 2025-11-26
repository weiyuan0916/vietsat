# License Key Check API

## Simple License Key Status Check

### Endpoint
```
POST /api/v1/licenses/check-key
```

### Description
This is a simple API endpoint that checks if a license key is active and working. It only requires the license key as a parameter and returns a simple true/false status.

### Request

**Method:** `POST`  
**URL:** `https://yourdomain.com/api/v1/licenses/check-key`  
**Content-Type:** `application/json`

**Request Body:**
```json
{
  "license_key": "LS-XXXX-XXXX-XXXX-XXXX"
}
```

### Response

**Success Response:**
```json
{
  "status": true
}
```

**Failed Response:**
```json
{
  "status": false
}
```

### Status Logic

The API returns:
- `"status": true` - License is **active** and **not expired**
- `"status": false` - License is **expired**, **deactivated**, **invalid**, or **not found**

### Usage Examples

#### cURL Example
```bash
curl -X POST https://yourdomain.com/api/v1/licenses/check-key \
  -H "Content-Type: application/json" \
  -d '{
    "license_key": "LS-1234-5678-9012-3456"
  }'
```

#### JavaScript Example
```javascript
const checkLicenseKey = async (licenseKey) => {
  try {
    const response = await fetch('https://yourdomain.com/api/v1/licenses/check-key', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        license_key: licenseKey
      })
    });
    
    const result = await response.json();
    return result.status; // true or false
  } catch (error) {
    console.error('Error checking license:', error);
    return false;
  }
};

// Usage
const isValid = await checkLicenseKey('LS-1234-5678-9012-3456');
if (isValid) {
  console.log('License is active and working');
} else {
  console.log('License is not valid or expired');
}
```

#### PHP Example
```php
<?php
function checkLicenseKey($licenseKey) {
    $url = 'https://yourdomain.com/api/v1/licenses/check-key';
    
    $data = json_encode([
        'license_key' => $licenseKey
    ]);
    
    $options = [
        'http' => [
            'header' => "Content-Type: application/json\r\n",
            'method' => 'POST',
            'content' => $data
        ]
    ];
    
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    
    if ($result === FALSE) {
        return false;
    }
    
    $response = json_decode($result, true);
    return $response['status'] ?? false;
}

// Usage
$licenseKey = 'LS-1234-5678-9012-3456';
$isValid = checkLicenseKey($licenseKey);

if ($isValid) {
    echo "License is active and working";
} else {
    echo "License is not valid or expired";
}
?>
```

#### Python Example
```python
import requests
import json

def check_license_key(license_key):
    url = 'https://yourdomain.com/api/v1/licenses/check-key'
    
    payload = {
        'license_key': license_key
    }
    
    try:
        response = requests.post(url, json=payload)
        result = response.json()
        return result.get('status', False)
    except Exception as e:
        print(f"Error checking license: {e}")
        return False

# Usage
license_key = 'LS-1234-5678-9012-3456'
is_valid = check_license_key(license_key)

if is_valid:
    print("License is active and working")
else:
    print("License is not valid or expired")
```

### Error Handling

The API is designed to be simple and always returns HTTP 200 with a JSON response. Any error condition (invalid key, expired license, server error, etc.) will result in `"status": false`.

### Integration Tips

1. **Simple Integration**: This endpoint is perfect for quick license validation in your application startup
2. **Lightweight**: Minimal response payload for fast checking
3. **Error Safe**: Always returns a boolean status, never throws exceptions
4. **No Dependencies**: Only requires the license key, no machine ID or other parameters needed

### Differences from Other Endpoints

- **`/check-key`**: Simple true/false status (this endpoint)
- **`/validate`**: Detailed validation with machine binding and expiration info
- **`/check-status`**: Lightweight status with days remaining and version info

Use `/check-key` when you only need to know if a license is working or not.
