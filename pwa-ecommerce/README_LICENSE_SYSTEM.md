# 🔑 License Management System - Complete Guide

## Overview

A comprehensive **License Management System** for Laravel 12 with **automatic app updates**, **version control**, and **file distribution**.

Perfect for desktop apps (Windows, macOS), mobile apps (Android, iOS), and any software that needs license verification and automatic updates.

---

## ⚡ Features

### 🎫 License Management
- ✅ Generate unique license keys
- ✅ Set expiration dates
- ✅ Control max activations per license
- ✅ Track active devices/machines
- ✅ License types: Trial, Standard, Premium, Enterprise
- ✅ License status: Active, Suspended, Revoked, Expired
- ✅ Renewal system
- ✅ Metadata support

### 📱 Version Control
- ✅ Set minimum required app version
- ✅ Track latest available version
- ✅ Force updates for security patches
- ✅ Optional updates for new features
- ✅ Semantic versioning (1.0.0)

### 📦 File Distribution
- ✅ Upload update files (.exe, .apk, .ipa, .dmg, .zip)
- ✅ Automatic download URL generation
- ✅ File size tracking (up to 500MB+)
- ✅ Secure file delivery (license-protected)
- ✅ Streamed downloads (memory-efficient)

### 🔐 Security
- ✅ License key validation
- ✅ Machine ID binding
- ✅ Expiration enforcement
- ✅ Activation limits
- ✅ Secure file access (not publicly exposed)
- ✅ Revocation support

### 🎛️ Admin Panel (Filament)
- ✅ CRUD for licenses
- ✅ File upload interface
- ✅ Version management
- ✅ Activation tracking
- ✅ Stats dashboard
- ✅ Search & filters

---

## 🚀 Quick Start

### 1. Installation

The system is already installed and migrated!

**Database Tables:**
- `licenses` - License information
- `license_activations` - Machine activations

**Migrations Run:**
- ✅ `create_licenses_table`
- ✅ `create_license_activations_table`
- ✅ `add_version_fields_to_licenses_table`
- ✅ `add_update_file_to_licenses_table`

### 2. Create Your First License

**Via Filament Admin:**

1. Navigate to **License Management → Licenses**
2. Click **New License**
3. Fill in:
   - **License Key**: Auto-generated (e.g., `LS-ABCD-1234-EFGH-5678`)
   - **Type**: Standard
   - **Status**: Active
   - **Max Activations**: 3
   - **Expires At**: +1 year
4. Expand **Version Control**:
   - **Min App Version**: `1.0.0`
   - **Latest App Version**: `1.2.0`
   - **Force Update**: ✅ (optional)
   - **Upload Update File**: Select your `.exe`, `.apk`, etc.
5. Click **Save**

**Via Tinker:**

```php
php artisan tinker

$license = \App\Models\License::create([
    'license_key' => \App\Models\License::generateKey(),
    'type' => 'standard',
    'status' => 'active',
    'max_activations' => 3,
    'issued_at' => now(),
    'expires_at' => now()->addYear(),
    'min_app_version' => '1.0.0',
    'latest_app_version' => '1.2.0',
    'force_update' => false,
]);

echo "License Key: " . $license->license_key;
```

### 3. Integrate Into Your App

See client implementation examples below ↓

---

## 📚 Documentation Files

| File | Description |
|------|-------------|
| **LICENSE_FILE_UPLOAD_GUIDE.md** | Complete guide for file upload & download |
| **LICENSE_VERSION_CONTROL_GUIDE.md** | Version checking and update requirements |
| **LICENSE_AUTO_UPDATE_SUMMARY.md** | Quick reference for auto-update feature |
| **README_LICENSE_SYSTEM.md** | This file - Complete overview |

---

## 🌐 API Endpoints

### Base URL: `/api/v1/licenses`

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/activate` | Activate license on a device |
| POST | `/validate` | Validate license is active |
| POST | `/check-status` | Quick status check |
| POST | `/check-update` | Check for app updates |
| GET | `/download-update/{key}` | Download update file |
| POST | `/renew` | Renew expired license |
| POST | `/deactivate` | Remove device activation |
| GET | `/{licenseKey}` | Get license details |

---

## 💻 Client Integration Examples

### Windows Desktop App (C# / WPF)

```csharp
public class LicenseManager
{
    private const string API_BASE = "https://yourapi.com/api/v1/licenses";
    private readonly HttpClient _client = new HttpClient();

    // On app startup
    public async Task<bool> ValidateAndCheckUpdate()
    {
        // Step 1: Validate license
        var validateRequest = new
        {
            license_key = Settings.LicenseKey,
            machine_id = GetMachineId(),
            app_version = Assembly.GetExecutingAssembly().GetName().Version.ToString()
        };

        var response = await _client.PostAsJsonAsync($"{API_BASE}/validate", validateRequest);
        var result = await response.Content.ReadFromJsonAsync<LicenseResponse>();

        if (!result.Data.Valid)
        {
            ShowLicenseInvalidDialog();
            Application.Current.Shutdown();
            return false;
        }

        // Step 2: Check for updates
        var versionStatus = result.Data.VersionStatus;

        if (versionStatus?.RequiresUpdate == true)
        {
            // Force update
            await DownloadAndInstall(versionStatus.DownloadUrl);
            return false;
        }

        if (versionStatus?.HasUpdate == true)
        {
            // Optional update
            var update = MessageBox.Show(
                $"Version {versionStatus.LatestVersion} is available. Update now?",
                "Update Available",
                MessageBoxButton.YesNo
            );

            if (update == MessageBoxResult.Yes)
            {
                await DownloadAndInstall(versionStatus.DownloadUrl);
                return false;
            }
        }

        return true;
    }

    private string GetMachineId()
    {
        // Generate unique machine identifier
        return Environment.MachineName + "-" + 
               Environment.UserName + "-" + 
               GetCpuId();
    }
}
```

### Android App (Kotlin)

```kotlin
class LicenseManager(private val context: Context) {
    
    companion object {
        const val API_BASE = "https://yourapi.com/api/v1/licenses"
    }

    suspend fun activateLicense(licenseKey: String): Boolean {
        return withContext(Dispatchers.IO) {
            val request = ActivateLicenseRequest(
                license_key = licenseKey,
                machine_id = getMachineId(),
                machine_name = Build.MODEL,
                hardware_info = mapOf(
                    "manufacturer" to Build.MANUFACTURER,
                    "model" to Build.MODEL,
                    "android_version" to Build.VERSION.RELEASE
                )
            )

            try {
                val response = apiClient.post("$API_BASE/activate", request)
                
                if (response.success) {
                    // Save license key
                    PreferenceManager.getDefaultSharedPreferences(context)
                        .edit()
                        .putString("license_key", licenseKey)
                        .apply()
                    
                    return@withContext true
                }
            } catch (e: Exception) {
                Log.e("LicenseManager", "Activation failed", e)
            }
            
            return@withContext false
        }
    }

    suspend fun checkForUpdates(): UpdateStatus? {
        val licenseKey = PreferenceManager.getDefaultSharedPreferences(context)
            .getString("license_key", null) ?: return null

        val appVersion = BuildConfig.VERSION_NAME

        return withContext(Dispatchers.IO) {
            try {
                val response = apiClient.post("$API_BASE/check-update") {
                    body = CheckUpdateRequest(licenseKey, appVersion)
                }

                if (response.success) {
                    return@withContext response.data
                }
            } catch (e: Exception) {
                Log.e("LicenseManager", "Update check failed", e)
            }

            return@withContext null
        }
    }

    private fun getMachineId(): String {
        return Settings.Secure.getString(
            context.contentResolver,
            Settings.Secure.ANDROID_ID
        )
    }
}
```

### Electron App (JavaScript/TypeScript)

```javascript
const { app } = require('electron');
const axios = require('axios');
const machineId = require('node-machine-id');

const API_BASE = 'https://yourapi.com/api/v1/licenses';

class LicenseManager {
  
  async validateLicense(licenseKey) {
    try {
      const response = await axios.post(`${API_BASE}/validate`, {
        license_key: licenseKey,
        machine_id: machineId.machineIdSync(),
        app_version: app.getVersion(),
      });

      const { success, data } = response.data;

      if (!success || !data.valid) {
        return { valid: false, message: data.message };
      }

      // Check for updates
      if (data.version_status?.requires_update) {
        // Force update
        await this.downloadAndInstall(data.version_status.download_url);
        return { valid: true, updating: true };
      }

      if (data.version_status?.has_update) {
        // Notify user of optional update
        this.notifyUpdateAvailable(data.version_status);
      }

      return { valid: true, data };

    } catch (error) {
      console.error('License validation failed:', error);
      return { valid: false, error };
    }
  }

  async activateLicense(licenseKey) {
    try {
      const response = await axios.post(`${API_BASE}/activate`, {
        license_key: licenseKey,
        machine_id: machineId.machineIdSync(),
        machine_name: require('os').hostname(),
        ip_address: await this.getPublicIP(),
        hardware_info: {
          platform: process.platform,
          arch: process.arch,
          cpus: require('os').cpus().length,
          memory: require('os').totalmem(),
        },
      });

      return response.data;

    } catch (error) {
      console.error('License activation failed:', error);
      return { success: false, error };
    }
  }

  async getPublicIP() {
    try {
      const response = await axios.get('https://api.ipify.org?format=json');
      return response.data.ip;
    } catch {
      return null;
    }
  }
}

module.exports = LicenseManager;
```

### Flutter App (Dart)

```dart
import 'package:http/http.dart' as http;
import 'dart:convert';
import 'package:device_info_plus/device_info_plus.dart';
import 'package:package_info_plus/package_info_plus.dart';

class LicenseManager {
  static const String apiBase = 'https://yourapi.com/api/v1/licenses';

  Future<Map<String, dynamic>> validateLicense(String licenseKey) async {
    final deviceInfo = DeviceInfoPlugin();
    final packageInfo = await PackageInfo.fromPlatform();
    
    String machineId;
    if (Platform.isAndroid) {
      final androidInfo = await deviceInfo.androidInfo;
      machineId = androidInfo.id;
    } else if (Platform.isIOS) {
      final iosInfo = await deviceInfo.iosInfo;
      machineId = iosInfo.identifierForVendor ?? 'unknown';
    } else {
      machineId = 'unknown';
    }

    final response = await http.post(
      Uri.parse('$apiBase/validate'),
      headers: {'Content-Type': 'application/json'},
      body: jsonEncode({
        'license_key': licenseKey,
        'machine_id': machineId,
        'app_version': packageInfo.version,
      }),
    );

    final data = jsonDecode(response.body);

    if (data['success'] && data['data']['valid']) {
      // Check for updates
      final versionStatus = data['data']['version_status'];
      
      if (versionStatus != null) {
        if (versionStatus['requires_update']) {
          // Show force update dialog
          await _showForceUpdateDialog(versionStatus);
        } else if (versionStatus['has_update']) {
          // Show optional update dialog
          await _showOptionalUpdateDialog(versionStatus);
        }
      }
    }

    return data;
  }

  Future<void> _showForceUpdateDialog(Map<String, dynamic> versionStatus) async {
    // Implementation depends on your UI framework
    // Show dialog that blocks app usage until update
  }

  Future<void> _showOptionalUpdateDialog(Map<String, dynamic> versionStatus) async {
    // Show dialog with option to update or continue
  }
}
```

---

## 🔄 Typical Workflows

### Workflow 1: First-Time Activation

```
User enters license key
    ↓
App calls /activate with machine_id
    ↓
Server validates key
    ↓
If valid → Save activation
    ↓
Return success
    ↓
App stores license key locally
    ↓
App continues
```

### Workflow 2: App Startup (License Check)

```
App starts
    ↓
Load saved license key
    ↓
Call /validate with license + machine_id + app_version
    ↓
Server checks:
  - License valid?
  - Not expired?
  - Machine activated?
  - App version compatible?
    ↓
If requires_update = true:
  → Show "Update Required"
  → Download from download_url
  → Install update
  → Restart app
    ↓
If has_update = true:
  → Show "Update Available" notification
  → User can choose to update or skip
    ↓
Continue running app
```

### Workflow 3: Forced Security Update

```
Admin uploads critical security patch:
  - Upload MyApp-v1.2.1.exe
  - Set min_app_version = "1.2.1"
  - Enable force_update = true
    ↓
User opens app (version 1.0.0)
    ↓
App calls /check-update
    ↓
Server responds:
  - requires_update = true
  - download_url = "..."
    ↓
App shows:
  "Critical security update required.
   Downloading version 1.2.1..."
    ↓
App downloads file
    ↓
App launches installer
    ↓
Old app closes
    ↓
User installs new version
```

---

## 🎛️ Admin Panel Features

### Dashboard
- Total licenses (active/expired/revoked)
- Active activations
- Expiring soon alerts
- Recent activations

### License Management
- Create/Edit/Delete licenses
- Search & filter
- Bulk actions
- Export to CSV

### Version Control
- Set minimum version per license
- Upload update files
- Track file versions
- File size monitoring

### Activation Tracking
- View all activations per license
- Machine details (name, IP, hardware)
- Deactivate specific machines
- Last check-in time

---

## 🔐 Security Best Practices

### For Admins:

1. ✅ **Regular Updates** - Keep min_app_version current
2. ✅ **Monitor Activations** - Watch for unusual patterns
3. ✅ **Revoke Compromised Licenses** - Immediately disable leaked keys
4. ✅ **Set Appropriate Expiration** - Don't make licenses too long
5. ✅ **Backup Files** - Keep update files backed up

### For Developers:

1. ✅ **Obfuscate License Keys** - Don't store in plain text
2. ✅ **Validate on Startup** - Always check license when app opens
3. ✅ **Handle Offline Mode** - Cache last validation (time-limited)
4. ✅ **Unique Machine IDs** - Use hardware-based identifiers
5. ✅ **HTTPS Only** - Never call API over HTTP
6. ✅ **Error Handling** - Gracefully handle network errors

---

## 📊 API Response Examples

### Activate License (Success)

**Request:**
```json
POST /api/v1/licenses/activate
{
  "license_key": "LS-ABCD-1234-EFGH-5678",
  "machine_id": "WIN-DESKTOP-XYZ",
  "machine_name": "John's PC",
  "ip_address": "192.168.1.100",
  "hardware_info": {
    "cpu": "Intel i7-9700K",
    "ram": "16GB",
    "os": "Windows 11"
  }
}
```

**Response:**
```json
{
  "success": true,
  "message": "License activated successfully",
  "data": {
    "license": {
      "license_key": "LS-ABCD-1234-EFGH-5678",
      "type": "standard",
      "status": "active",
      "max_activations": 3,
      "current_activations": 1,
      "expires_at": "2026-10-22 12:00:00",
      "days_remaining": 365
    },
    "activation": {
      "id": 1,
      "machine_id": "WIN-DESKTOP-XYZ",
      "machine_name": "John's PC",
      "activated_at": "2025-10-22 12:00:00"
    }
  }
}
```

### Validate License (With Update Required)

**Request:**
```json
POST /api/v1/licenses/validate
{
  "license_key": "LS-ABCD-1234-EFGH-5678",
  "machine_id": "WIN-DESKTOP-XYZ",
  "app_version": "0.9.0"
}
```

**Response:**
```json
{
  "success": true,
  "message": "License is valid",
  "data": {
    "valid": true,
    "license": { /* ... */ },
    "days_remaining": 365,
    "expires_at": "2026-10-22 12:00:00",
    "version_status": {
      "current_version": "0.9.0",
      "min_version": "1.0.0",
      "latest_version": "1.2.0",
      "is_compatible": false,
      "has_update": true,
      "requires_update": true,
      "force_update": true,
      "download_url": "https://yourapi.com/api/v1/licenses/download-update/LS-ABCD-1234-EFGH-5678",
      "file_version": "1.2.0",
      "file_size": 52428800,
      "file_size_formatted": "50.0 MB"
    }
  }
}
```

---

## 🧪 Testing

### Test License Activation

```bash
curl -X POST http://localhost/api/v1/licenses/activate \
  -H "Content-Type: application/json" \
  -d '{
    "license_key": "LS-TEST-1234-5678-9ABC",
    "machine_id": "TEST-MACHINE-001",
    "machine_name": "Test PC"
  }'
```

### Test Update Check

```bash
curl -X POST http://localhost/api/v1/licenses/check-update \
  -H "Content-Type: application/json" \
  -d '{
    "license_key": "LS-TEST-1234-5678-9ABC",
    "app_version": "1.0.0"
  }'
```

### Test File Download

```bash
curl -O http://localhost/api/v1/licenses/download-update/LS-TEST-1234-5678-9ABC
```

---

## 🎉 Summary

This License Management System provides everything you need:

✅ **License Generation & Validation**  
✅ **Multi-Device Activation Control**  
✅ **Expiration Management**  
✅ **Version Checking**  
✅ **Forced Updates**  
✅ **File Distribution**  
✅ **Automatic Downloads**  
✅ **Admin Panel (Filament)**  
✅ **RESTful API**  
✅ **Security Features**  

**Perfect for:**
- Desktop applications (Windows, macOS, Linux)
- Mobile apps (Android, iOS)
- Cross-platform apps (Electron, Flutter)
- SaaS products
- Enterprise software

---

## 📞 Support

For questions or issues, refer to:
- API documentation in `routes/api.php`
- Model methods in `app/Models/License.php`
- Service layer in `app/Services/LicenseService.php`
- Detailed guides in documentation files

---

**Happy licensing! 🔑**

