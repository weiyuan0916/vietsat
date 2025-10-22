# License Version Control Guide

## Overview

The License System now includes **App Version Control** functionality. This allows you to enforce minimum app versions and notify users when updates are available.

---

## Features

### 1. **Minimum Version Requirement**
- Set a minimum app version required for each license
- Prevents outdated app versions from being used

### 2. **Latest Version Tracking**
- Track the latest available app version
- Inform users when updates are available

### 3. **Force Update**
- Optionally force users to update if their version is below minimum
- Ensures all users run compatible app versions

---

## Database Fields

Three new fields have been added to the `licenses` table:

| Field | Type | Description |
|-------|------|-------------|
| `min_app_version` | string | Minimum app version required (e.g., "1.0.0") |
| `latest_app_version` | string | Latest available version (e.g., "1.2.0") |
| `force_update` | boolean | Whether to force update if below minimum |

---

## API Usage

### 1. Check App Update Status

**Endpoint:** `POST /api/v1/licenses/check-update`

**Request:**
```json
{
  "license_key": "LS-XXXX-XXXX-XXXX-XXXX",
  "app_version": "1.0.0"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "current_version": "1.0.0",
    "min_version": "1.0.0",
    "latest_version": "1.2.0",
    "is_compatible": true,
    "has_update": true,
    "requires_update": false,
    "force_update": false
  }
}
```

**Response Fields:**
- `current_version` - The app version sent in request
- `min_version` - Minimum version required by license
- `latest_version` - Latest version available
- `is_compatible` - Whether current version meets minimum requirement
- `has_update` - Whether a newer version is available
- `requires_update` - Whether user MUST update (force_update + incompatible)
- `force_update` - Whether license has force update enabled

---

### 2. Validate License with Version Check

**Endpoint:** `POST /api/v1/licenses/validate`

**Request:**
```json
{
  "license_key": "LS-XXXX-XXXX-XXXX-XXXX",
  "machine_id": "unique-machine-identifier",
  "app_version": "1.0.0"
}
```

**Response:**
```json
{
  "success": true,
  "message": "License is valid",
  "data": {
    "valid": true,
    "license": { /* License Resource */ },
    "days_remaining": 365,
    "expires_at": "2026-10-22 12:00:00",
    "version_status": {
      "current_version": "1.0.0",
      "min_version": "1.0.0",
      "latest_version": "1.2.0",
      "is_compatible": true,
      "has_update": true,
      "requires_update": false,
      "force_update": false
    }
  }
}
```

---

### 3. Check Status with Version

**Endpoint:** `POST /api/v1/licenses/check-status`

**Request:**
```json
{
  "license_key": "LS-XXXX-XXXX-XXXX-XXXX",
  "machine_id": "unique-machine-identifier",
  "app_version": "1.0.0"
}
```

**Response:**
```json
{
  "success": true,
  "valid": true,
  "days_remaining": 365,
  "expires_at": "2026-10-22 12:00:00",
  "version_status": {
    "current_version": "1.0.0",
    "min_version": "1.0.0",
    "latest_version": "1.2.0",
    "is_compatible": true,
    "has_update": true,
    "requires_update": false,
    "force_update": false
  }
}
```

---

## Model Methods

### `isVersionCompatible(string $appVersion): bool`
Check if the app version meets minimum requirements.

```php
$license = License::find(1);
$isCompatible = $license->isVersionCompatible('1.0.0');
```

### `hasUpdateAvailable(string $appVersion): bool`
Check if an update is available.

```php
$hasUpdate = $license->hasUpdateAvailable('1.0.0');
```

### `requiresUpdate(string $appVersion): bool`
Check if user MUST update before using the app.

```php
$mustUpdate = $license->requiresUpdate('0.9.0');
```

### `getVersionStatus(string $appVersion): array`
Get complete version status information.

```php
$status = $license->getVersionStatus('1.0.0');
```

---

## Admin Panel Management

### Setting Version Requirements

1. Navigate to **License Management → Licenses**
2. Create or edit a license
3. Expand the **Version Control** section
4. Configure:
   - **Minimum App Version:** Required version (e.g., "1.0.0")
   - **Latest App Version:** Current latest version (e.g., "1.2.0")
   - **Force Update:** Toggle to force updates

### Version Control Section Fields

- **Minimum App Version**
  - Format: Semantic versioning (e.g., "1.0.0")
  - Users below this version will be flagged as incompatible
  
- **Latest App Version**
  - Format: Semantic versioning (e.g., "1.2.0")
  - Used to notify users of available updates
  
- **Force Update**
  - When enabled: Users below minimum version MUST update
  - When disabled: Users below minimum version will be warned but can still use the app

---

## Client-Side Implementation

### Example: Flutter/Dart

```dart
class LicenseService {
  Future<bool> checkAndValidateVersion(String appVersion) async {
    final response = await http.post(
      Uri.parse('https://yourapi.com/api/v1/licenses/check-update'),
      body: jsonEncode({
        'license_key': licenseKey,
        'app_version': appVersion,
      }),
    );

    final data = jsonDecode(response.body);
    
    if (data['success']) {
      final versionStatus = data['data'];
      
      // Check if update is required
      if (versionStatus['requires_update']) {
        // Show "Update Required" dialog - block app usage
        showUpdateRequiredDialog();
        return false;
      }
      
      // Check if update is available (but not required)
      if (versionStatus['has_update']) {
        // Show "Update Available" notification
        showUpdateAvailableNotification();
      }
      
      return true;
    }
    
    return false;
  }
}
```

### Example: React Native / JavaScript

```javascript
async function checkAppVersion(appVersion) {
  const response = await fetch('https://yourapi.com/api/v1/licenses/check-update', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      license_key: LICENSE_KEY,
      app_version: appVersion,
    }),
  });

  const { success, data } = await response.json();
  
  if (success) {
    const { requires_update, has_update, latest_version } = data;
    
    if (requires_update) {
      // Block app and force update
      Alert.alert(
        'Update Required',
        `Please update to version ${latest_version} to continue`,
        [{ text: 'Update Now', onPress: () => openAppStore() }],
        { cancelable: false }
      );
      return false;
    }
    
    if (has_update) {
      // Show optional update notification
      Alert.alert(
        'Update Available',
        `Version ${latest_version} is now available`,
        [
          { text: 'Later', style: 'cancel' },
          { text: 'Update', onPress: () => openAppStore() }
        ]
      );
    }
  }
  
  return true;
}
```

---

## Version Comparison Logic

The system uses PHP's `version_compare()` function for semantic versioning:

- **1.0.0** < **1.0.1** < **1.1.0** < **2.0.0**
- Supports major.minor.patch format
- Also supports: 1.0, 1.0.0-beta, etc.

---

## Best Practices

### 1. **Set Minimum Version Carefully**
- Don't set it too high initially
- Gradually increase as you phase out old versions

### 2. **Update Latest Version Regularly**
- Keep `latest_app_version` current when you release updates
- Helps users stay informed

### 3. **Use Force Update Sparingly**
- Enable `force_update` only for critical security updates
- Otherwise, users may be frustrated by forced updates

### 4. **Test Before Enabling**
- Always test version checking logic before production
- Verify that users on various versions are handled correctly

### 5. **Communication**
- Include release notes in your update notifications
- Explain why an update is required

---

## Example Scenarios

### Scenario 1: Optional Update Available

**License Settings:**
- `min_app_version`: "1.0.0"
- `latest_app_version`: "1.2.0"
- `force_update`: false

**User App Version:** "1.1.0"

**Result:**
```json
{
  "is_compatible": true,
  "has_update": true,
  "requires_update": false
}
```
→ User can continue using app, but is notified of update

---

### Scenario 2: Update Required

**License Settings:**
- `min_app_version`: "1.2.0"
- `latest_app_version`: "1.3.0"
- `force_update`: true

**User App Version:** "1.1.0"

**Result:**
```json
{
  "is_compatible": false,
  "has_update": true,
  "requires_update": true
}
```
→ User MUST update to continue using app

---

### Scenario 3: No Version Requirement

**License Settings:**
- `min_app_version`: null
- `latest_app_version`: null
- `force_update`: false

**User App Version:** "0.5.0"

**Result:**
```json
{
  "is_compatible": true,
  "has_update": false,
  "requires_update": false
}
```
→ No version restrictions applied

---

## Troubleshooting

### Issue: Version always shows as incompatible

**Solution:** Check version format
- Use semantic versioning: "1.0.0"
- Avoid prefixes like "v1.0.0"

### Issue: Force update not working

**Solution:** Verify both conditions are met:
1. `force_update` is `true`
2. User's version is below `min_app_version`

### Issue: API returns no version_status

**Solution:** Ensure `app_version` is included in request:
```json
{
  "license_key": "LS-...",
  "app_version": "1.0.0"  ← Required
}
```

---

## Summary

The License Version Control system provides:

✅ Enforce minimum app versions  
✅ Notify users of available updates  
✅ Force critical security updates  
✅ Flexible version management per license  
✅ Easy integration with any client app  
✅ Admin-friendly UI in Filament panel  

For questions or support, refer to the API documentation at `/api/v1/licenses/*`

