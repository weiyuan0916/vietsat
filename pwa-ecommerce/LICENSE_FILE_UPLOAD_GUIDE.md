# License File Upload & Auto-Update Guide

## Overview

The License System now supports **uploading update files** (e.g., `.exe`, `.apk`, `.ipa`, `.dmg`) that users can automatically download when updates are required or available.

---

## 🎯 Features

### 1. **File Upload in Admin Panel**
- Upload update files up to 500MB
- Supports: `.exe`, `.apk`, `.ipa`, `.dmg`, `.zip`
- Auto-calculates file size
- Auto-fills version from "Latest App Version"

### 2. **Automatic Download URL Generation**
- Each license gets a unique download URL
- Secure: Only valid licenses can download
- Direct file streaming (no redirect)

### 3. **Version + File Management**
- Track file version separately from app version
- Display formatted file size (e.g., "50.0 MB")
- Check if file exists before showing download link

### 4. **Automatic Update Flow**
- When `force_update = true` and user's version is below minimum
- App receives `download_url` in API response
- App automatically downloads and installs update

---

## 📝 Database Schema

New fields added to `licenses` table:

| Field | Type | Description |
|-------|------|-------------|
| `update_file_path` | string | Path to update file in storage |
| `update_file_version` | string | Version of the uploaded file |
| `update_file_size` | bigInteger | File size in bytes |

---

## 🎛️ Admin Panel Usage

### Step 1: Navigate to License

1. Go to **License Management → Licenses**
2. Create or edit a license
3. Expand the **Version Control** section

### Step 2: Set Version Requirements

- **Minimum App Version**: `1.0.0` (users below this need to update)
- **Latest App Version**: `1.2.0` (newest version available)
- **Force Update**: ✅ Enable (to require updates)

### Step 3: Upload Update File

1. Click **"Update File"** field
2. Select your app installer:
   - Windows: `.exe` file
   - Android: `.apk` file
   - iOS: `.ipa` file
   - macOS: `.dmg` file
   - Cross-platform: `.zip` archive
3. File uploads to `storage/app/license-updates/`
4. **File Size** is auto-calculated
5. **Update File Version** is auto-filled from "Latest App Version"

### Step 4: Save

Click **Save** – Your update file is now ready for download!

---

## 🚀 API Usage

### 1. Check for Updates

**Endpoint:** `POST /api/v1/licenses/check-update`

**Request:**
```json
{
  "license_key": "LS-XXXX-XXXX-XXXX-XXXX",
  "app_version": "1.0.0"
}
```

**Response (Update Available):**
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
    "force_update": false,
    "download_url": "https://yourapi.com/api/v1/licenses/download-update/LS-XXXX-XXXX-XXXX-XXXX",
    "file_version": "1.2.0",
    "file_size": 52428800,
    "file_size_formatted": "50.0 MB"
  }
}
```

**Response (Update Required):**
```json
{
  "success": true,
  "data": {
    "current_version": "0.9.0",
    "min_version": "1.0.0",
    "latest_version": "1.2.0",
    "is_compatible": false,
    "has_update": true,
    "requires_update": true,
    "force_update": true,
    "download_url": "https://yourapi.com/api/v1/licenses/download-update/LS-XXXX-XXXX-XXXX-XXXX",
    "file_version": "1.2.0",
    "file_size": 52428800,
    "file_size_formatted": "50.0 MB"
  }
}
```

### 2. Download Update File

**Endpoint:** `GET /api/v1/licenses/download-update/{licenseKey}`

**Example:**
```
GET https://yourapi.com/api/v1/licenses/download-update/LS-ABCD-1234-EFGH-5678
```

**Response:**
- **Success:** Binary file stream with proper headers
- **Error (404):** License not found or no update file
- **Error (403):** License invalid or expired

**Headers:**
```
Content-Type: application/x-msdownload (for .exe)
Content-Disposition: attachment; filename="MyApp-v1.2.0.exe"
Content-Length: 52428800
Cache-Control: no-cache, must-revalidate
```

---

## 💻 Client Implementation

### Example 1: Windows Desktop App (C# / WPF)

```csharp
using System.Net.Http;
using System.Text.Json;

public class UpdateService
{
    private readonly HttpClient _httpClient;
    private const string API_BASE = "https://yourapi.com/api/v1/licenses";

    public async Task<bool> CheckAndDownloadUpdate(string licenseKey, string currentVersion)
    {
        // Step 1: Check for updates
        var checkRequest = new
        {
            license_key = licenseKey,
            app_version = currentVersion
        };

        var response = await _httpClient.PostAsJsonAsync($"{API_BASE}/check-update", checkRequest);
        var result = await response.Content.ReadFromJsonAsync<UpdateCheckResponse>();

        if (!result.Success) return false;

        var data = result.Data;

        // Step 2: If update required, download automatically
        if (data.RequiresUpdate && data.DownloadUrl != null)
        {
            // Show "Update Required" dialog
            var dialogResult = MessageBox.Show(
                $"Version {data.LatestVersion} is required.\n\n" +
                $"Download Size: {data.FileSizeFormatted}\n\n" +
                "The update will download and install automatically.",
                "Update Required",
                MessageBoxButton.OK,
                MessageBoxImage.Warning
            );

            // Download and install
            await DownloadAndInstall(data.DownloadUrl);
            return true;
        }

        // Step 3: If update available (but not required), prompt user
        if (data.HasUpdate && data.DownloadUrl != null)
        {
            var dialogResult = MessageBox.Show(
                $"Version {data.LatestVersion} is available.\n\n" +
                $"Download Size: {data.FileSizeFormatted}\n\n" +
                "Would you like to update now?",
                "Update Available",
                MessageBoxButton.YesNo,
                MessageBoxImage.Information
            );

            if (dialogResult == MessageBoxResult.Yes)
            {
                await DownloadAndInstall(data.DownloadUrl);
                return true;
            }
        }

        return false;
    }

    private async Task DownloadAndInstall(string downloadUrl)
    {
        // Download to temp directory
        var fileName = $"update_{DateTime.Now:yyyyMMddHHmmss}.exe";
        var filePath = Path.Combine(Path.GetTempPath(), fileName);

        using (var fileStream = new FileStream(filePath, FileMode.Create))
        {
            var response = await _httpClient.GetAsync(downloadUrl);
            await response.Content.CopyToAsync(fileStream);
        }

        // Launch installer and close current app
        Process.Start(filePath);
        Application.Current.Shutdown();
    }
}
```

### Example 2: Android App (Kotlin)

```kotlin
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.withContext
import okhttp3.OkHttpClient
import okhttp3.Request
import java.io.File

class UpdateManager(private val context: Context) {
    
    suspend fun checkAndDownloadUpdate(licenseKey: String, currentVersion: String): Boolean {
        return withContext(Dispatchers.IO) {
            // Step 1: Check for updates
            val response = apiClient.post("/licenses/check-update") {
                body = UpdateCheckRequest(licenseKey, currentVersion)
            }
            
            val data = response.data ?: return@withContext false
            
            // Step 2: Handle required update
            if (data.requiresUpdate && data.downloadUrl != null) {
                withContext(Dispatchers.Main) {
                    showUpdateRequiredDialog(data)
                }
                downloadAndInstall(data.downloadUrl)
                return@withContext true
            }
            
            // Step 3: Handle optional update
            if (data.hasUpdate && data.downloadUrl != null) {
                val userWantsUpdate = withContext(Dispatchers.Main) {
                    showUpdateAvailableDialog(data)
                }
                
                if (userWantsUpdate) {
                    downloadAndInstall(data.downloadUrl)
                    return@withContext true
                }
            }
            
            return@withContext false
        }
    }
    
    private suspend fun downloadAndInstall(downloadUrl: String) {
        withContext(Dispatchers.IO) {
            val client = OkHttpClient()
            val request = Request.Builder().url(downloadUrl).build()
            
            client.newCall(request).execute().use { response ->
                val file = File(context.getExternalFilesDir(null), "update.apk")
                
                response.body?.byteStream()?.use { input ->
                    file.outputStream().use { output ->
                        input.copyTo(output)
                    }
                }
                
                // Install APK
                val installIntent = Intent(Intent.ACTION_VIEW).apply {
                    setDataAndType(
                        FileProvider.getUriForFile(context, "${context.packageName}.provider", file),
                        "application/vnd.android.package-archive"
                    )
                    addFlags(Intent.FLAG_GRANT_READ_URI_PERMISSION)
                    addFlags(Intent.FLAG_ACTIVITY_NEW_TASK)
                }
                
                context.startActivity(installIntent)
            }
        }
    }
}
```

### Example 3: Electron App (JavaScript)

```javascript
const { app, dialog } = require('electron');
const https = require('https');
const fs = require('fs');
const path = require('path');
const { exec } = require('child_process');

async function checkAndDownloadUpdate(licenseKey, currentVersion) {
  try {
    // Step 1: Check for updates
    const response = await fetch('https://yourapi.com/api/v1/licenses/check-update', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        license_key: licenseKey,
        app_version: currentVersion,
      }),
    });

    const { success, data } = await response.json();
    
    if (!success || !data) return false;

    // Step 2: Handle required update
    if (data.requires_update && data.download_url) {
      const result = await dialog.showMessageBox({
        type: 'warning',
        title: 'Update Required',
        message: `Version ${data.latest_version} is required`,
        detail: `Download Size: ${data.file_size_formatted}\n\nThe app will close after download.`,
        buttons: ['Download Now'],
        defaultId: 0,
        cancelId: -1,
      });

      if (result.response === 0) {
        await downloadAndInstall(data.download_url);
        return true;
      }
    }

    // Step 3: Handle optional update
    if (data.has_update && data.download_url) {
      const result = await dialog.showMessageBox({
        type: 'info',
        title: 'Update Available',
        message: `Version ${data.latest_version} is available`,
        detail: `Download Size: ${data.file_size_formatted}\n\nWould you like to update now?`,
        buttons: ['Download', 'Later'],
        defaultId: 0,
        cancelId: 1,
      });

      if (result.response === 0) {
        await downloadAndInstall(data.download_url);
        return true;
      }
    }

    return false;
  } catch (error) {
    console.error('Update check failed:', error);
    return false;
  }
}

function downloadAndInstall(downloadUrl) {
  return new Promise((resolve, reject) => {
    const fileName = `update_${Date.now()}.exe`;
    const filePath = path.join(app.getPath('temp'), fileName);
    const file = fs.createWriteStream(filePath);

    https.get(downloadUrl, (response) => {
      response.pipe(file);

      file.on('finish', () => {
        file.close(() => {
          // Launch installer
          exec(`"${filePath}"`, (error) => {
            if (error) {
              reject(error);
            } else {
              // Close app
              app.quit();
              resolve();
            }
          });
        });
      });
    }).on('error', (err) => {
      fs.unlink(filePath, () => reject(err));
    });
  });
}

module.exports = { checkAndDownloadUpdate };
```

---

## 🔒 Security Features

### 1. **License Validation**
- Download endpoint requires valid license key
- License must be active (not suspended/revoked)
- License must not be expired

### 2. **File Storage**
- Files stored in `storage/app/license-updates/` (not publicly accessible)
- Served through controller (not direct URL)
- Proper MIME type headers prevent XSS

### 3. **File Size Limits**
- Max upload: 500MB (configurable in Filament)
- Streamed download (memory-efficient)

---

## 📊 File Storage Location

Update files are stored at:

```
storage/
└── app/
    └── license-updates/
        ├── MyApp-v1.2.0.exe
        ├── MyApp-v1.2.0.apk
        └── MyApp-v1.2.0.dmg
```

**Not publicly accessible** – Files are served through the API endpoint only.

---

## ⚙️ Configuration

### Increase Upload Limit (Optional)

If you need to upload files larger than 500MB:

**1. Update Filament Resource:**
```php
Forms\FileUpload::make('update_file_path')
    ->maxSize(1024000) // 1GB in KB
```

**2. Update `php.ini`:**
```ini
upload_max_filesize = 1G
post_max_size = 1G
max_execution_time = 300
```

**3. Update Nginx/Apache:**

**Nginx:**
```nginx
client_max_body_size 1G;
```

**Apache:**
```apache
LimitRequestBody 1073741824
```

---

## 📖 Workflow Summary

### Admin Side:

1. ✅ Set minimum version (e.g., `1.0.0`)
2. ✅ Set latest version (e.g., `1.2.0`)
3. ✅ Upload update file (`.exe`, `.apk`, etc.)
4. ✅ Enable "Force Update" (optional)
5. ✅ Save license

### Client Side:

1. ✅ App calls `/check-update` on startup
2. ✅ If `requires_update = true`:
   - Show "Update Required" dialog
   - Download file from `download_url`
   - Install update automatically
   - Close app
3. ✅ If `has_update = true` (but not required):
   - Show "Update Available" notification
   - User can choose to update or skip

---

## 🧪 Testing

### Test Scenario 1: Manual Download

**Setup:**
- Upload `MyApp-v1.2.0.exe` to a license
- Set `latest_app_version = "1.2.0"`

**Test:**
```bash
# Get download URL
curl -X POST https://yourapi.com/api/v1/licenses/check-update \
  -H "Content-Type: application/json" \
  -d '{"license_key": "LS-XXXX-XXXX-XXXX-XXXX", "app_version": "1.0.0"}'

# Download file
curl -O https://yourapi.com/api/v1/licenses/download-update/LS-XXXX-XXXX-XXXX-XXXX
```

**Expected:**
- File downloads successfully
- Filename: `MyApp-v1.2.0.exe`

### Test Scenario 2: Forced Update

**Setup:**
- `min_app_version = "1.0.0"`
- `force_update = true`
- `update_file_path` uploaded

**Test:**
```bash
curl -X POST https://yourapi.com/api/v1/licenses/check-update \
  -H "Content-Type: application/json" \
  -d '{"license_key": "LS-XXXX-XXXX-XXXX-XXXX", "app_version": "0.9.0"}'
```

**Expected Response:**
```json
{
  "requires_update": true,
  "download_url": "https://yourapi.com/api/v1/licenses/download-update/LS-XXXX-XXXX-XXXX-XXXX"
}
```

---

## 🎉 Benefits

✅ **Automatic Updates** – Users get the latest version without visiting website  
✅ **Forced Security Patches** – Ensure all users run secure versions  
✅ **Bandwidth Control** – Only valid licenses can download  
✅ **Version Tracking** – Know which file version each user should get  
✅ **Multi-Platform** – Support Windows, macOS, Linux, Android, iOS  
✅ **Large Files** – Support up to 500MB (configurable to more)  
✅ **Secure Delivery** – Files not publicly accessible  

---

## 📚 Related Documentation

- [LICENSE_VERSION_CONTROL_GUIDE.md](LICENSE_VERSION_CONTROL_GUIDE.md) - Version checking
- [LICENSE_SYSTEM_GUIDE.md](LICENSE_SYSTEM_GUIDE.md) - License basics

---

## 🐛 Troubleshooting

### Issue: "No update file available"

**Cause:** No file uploaded for the license.

**Solution:** 
1. Go to Filament admin
2. Edit the license
3. Upload a file in "Update File" field

### Issue: "Update file not found on server"

**Cause:** File was deleted from storage or moved.

**Solution:**
1. Re-upload the file in Filament
2. Or check `storage/app/license-updates/` directory

### Issue: Download is slow

**Cause:** Large file size or server bandwidth.

**Solution:**
- Use a CDN for file hosting
- Compress files (`.zip` format)
- Optimize server network

### Issue: "File size is 0 bytes"

**Cause:** `update_file_size` not calculated correctly.

**Solution:**
1. Re-upload the file
2. The `afterStateUpdated` callback will recalculate size

---

## 🚀 Future Enhancements

Potential improvements:

1. **Delta Updates** – Only download changed parts
2. **CDN Integration** – Host files on S3/CloudFront
3. **Checksums** – Verify file integrity (MD5/SHA256)
4. **Download Resume** – Support partial downloads
5. **Version Rollback** – Keep previous versions available
6. **Analytics** – Track download counts and success rates

---

For questions, refer to the API documentation or contact support.

