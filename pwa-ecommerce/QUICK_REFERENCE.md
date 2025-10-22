# 🚀 License System - Quick Reference Card

## 📋 Essential API Endpoints

```bash
# Base URL
https://yourapi.com/api/v1/licenses

# Activate License
POST /activate
{
  "license_key": "LS-XXXX-XXXX-XXXX-XXXX",
  "machine_id": "unique-id"
}

# Validate License + Check Updates
POST /validate
{
  "license_key": "LS-XXXX-XXXX-XXXX-XXXX",
  "machine_id": "unique-id",
  "app_version": "1.0.0"
}

# Check for Updates
POST /check-update
{
  "license_key": "LS-XXXX-XXXX-XXXX-XXXX",
  "app_version": "1.0.0"
}

# Download Update File
GET /download-update/{licenseKey}
```

---

## 🎯 Quick Integration (Any Language)

```javascript
// 1. ON APP INSTALL
const response = await fetch('https://yourapi.com/api/v1/licenses/activate', {
  method: 'POST',
  body: JSON.stringify({
    license_key: userEnteredKey,
    machine_id: getMachineId(),
  })
});

// 2. ON APP STARTUP
const response = await fetch('https://yourapi.com/api/v1/licenses/validate', {
  method: 'POST',
  body: JSON.stringify({
    license_key: savedLicenseKey,
    machine_id: getMachineId(),
    app_version: getCurrentVersion(),
  })
});

const { data } = await response.json();

// 3. HANDLE UPDATE
if (data.version_status?.requires_update) {
  // FORCE UPDATE
  downloadAndInstall(data.version_status.download_url);
} else if (data.version_status?.has_update) {
  // OPTIONAL UPDATE
  askUserToUpdate(data.version_status.download_url);
}
```

---

## 🎛️ Admin Panel Checklist

### Create License
1. Go to `/admin/licenses`
2. Click "New License"
3. Auto-generated key: `LS-XXXX-XXXX-XXXX-XXXX`
4. Set type, status, max activations, expiry
5. Save

### Enable Auto-Update
1. Edit license
2. Expand "Version Control"
3. Set:
   - Min App Version: `1.0.0`
   - Latest App Version: `1.2.0`
   - Force Update: ✅
4. Upload update file (.exe, .apk, etc.)
5. Save

---

## 📊 Response Status Meanings

| Field | Value | Meaning |
|-------|-------|---------|
| `valid` | `true` | License is active and not expired |
| `valid` | `false` | License invalid, expired, or revoked |
| `is_compatible` | `true` | App version meets minimum requirement |
| `is_compatible` | `false` | App version too old |
| `has_update` | `true` | Newer version available |
| `has_update` | `false` | Running latest version |
| `requires_update` | `true` | **MUST** update (force_update enabled) |
| `requires_update` | `false` | Can continue using current version |

---

## 🔐 Security Validations

API validates in this order:
1. ✅ License key exists?
2. ✅ Status = "active"? (not suspended/revoked)
3. ✅ Not expired?
4. ✅ Machine activated?
5. ✅ Version compatible? (if checking)

---

## 📁 File Storage

Update files stored at:
```
storage/app/license-updates/
```

**Not publicly accessible!**  
Only accessible via: `/api/v1/licenses/download-update/{key}`

---

## 🧪 Test Commands

```bash
# Test Activation
curl -X POST http://localhost/api/v1/licenses/activate \
  -H "Content-Type: application/json" \
  -d '{"license_key":"LS-TEST-1234-5678-9ABC","machine_id":"TEST-001"}'

# Test Validation
curl -X POST http://localhost/api/v1/licenses/validate \
  -H "Content-Type: application/json" \
  -d '{"license_key":"LS-TEST-1234-5678-9ABC","machine_id":"TEST-001","app_version":"1.0.0"}'

# Test Update Check
curl -X POST http://localhost/api/v1/licenses/check-update \
  -H "Content-Type: application/json" \
  -d '{"license_key":"LS-TEST-1234-5678-9ABC","app_version":"1.0.0"}'

# Test File Download
curl -O http://localhost/api/v1/licenses/download-update/LS-TEST-1234-5678-9ABC
```

---

## 📚 Documentation Files

| File | Purpose |
|------|---------|
| `README_LICENSE_SYSTEM.md` | **START HERE** - Complete overview |
| `LICENSE_FILE_UPLOAD_GUIDE.md` | File upload & download details |
| `LICENSE_VERSION_CONTROL_GUIDE.md` | Version management guide |
| `LICENSE_SYSTEM_FLOW.md` | Visual diagrams |
| `IMPLEMENTATION_COMPLETE.md` | Implementation checklist |
| `QUICK_REFERENCE.md` | This file |

---

## ⚡ Common Scenarios

### Scenario 1: Force Security Update

**Admin:**
- Upload `MyApp-v1.2.0.exe`
- Set `min_app_version = "1.2.0"`
- Enable `force_update = true`

**User (v1.0.0):**
- Opens app
- Sees: "Update Required"
- App auto-downloads and installs
- App restarts with v1.2.0

### Scenario 2: Optional Feature Update

**Admin:**
- Upload `MyApp-v1.3.0.exe`
- Set `latest_app_version = "1.3.0"`
- Keep `force_update = false`

**User (v1.2.0):**
- Opens app
- Sees: "Update Available"
- Can choose "Update" or "Later"
- App continues normally if skipped

### Scenario 3: No Update Available

**Admin:**
- `latest_app_version = "1.0.0"`

**User (v1.0.0):**
- Opens app
- No update notification
- App continues normally

---

## 🔑 License Key Format

Generated automatically:
```
LS-ABCD-1234-EFGH-5678
```

Format: `PREFIX-XXXX-XXXX-XXXX-XXXX`
- Prefix: `LS` (configurable)
- 4 segments of 4 random uppercase alphanumeric characters

---

## 🎨 Version Format

Use semantic versioning:
```
1.0.0
│ │ │
│ │ └─ Patch (bug fixes)
│ └─── Minor (new features)
└───── Major (breaking changes)
```

Examples:
- `1.0.0` → `1.0.1` (bug fix)
- `1.0.1` → `1.1.0` (new feature)
- `1.9.0` → `2.0.0` (major update)

---

## 🚨 Error Codes

| Error Code | Meaning |
|------------|---------|
| `INVALID_LICENSE` | License key not found |
| `LICENSE_EXPIRED` | License has expired |
| `LICENSE_REVOKED` | License was revoked by admin |
| `LICENSE_SUSPENDED` | License temporarily suspended |
| `MAX_ACTIVATIONS_REACHED` | Too many devices activated |
| `MACHINE_NOT_ACTIVATED` | This device not registered |
| `ACTIVATION_FAILED` | Could not activate license |
| `VALIDATION_FAILED` | Validation check failed |
| `NO_UPDATE_FILE` | No update file uploaded |
| `FILE_NOT_FOUND` | Update file missing from server |

---

## 💡 Pro Tips

1. **Always include `app_version`** in `/validate` and `/check-update` requests
2. **Store license key securely** in client app (encrypt if possible)
3. **Validate on startup** to check license status
4. **Handle offline mode** by caching last validation (with time limit)
5. **Show download progress** when downloading updates
6. **Use unique machine IDs** based on hardware (not easily spoofed)
7. **Test thoroughly** before deploying to production
8. **Keep backups** of update files
9. **Monitor activation patterns** for suspicious activity
10. **Set reasonable expiration dates** for licenses

---

## 📞 Need Help?

**Step 1:** Check the detailed guides
- `README_LICENSE_SYSTEM.md` - Complete overview
- `LICENSE_FILE_UPLOAD_GUIDE.md` - File upload help

**Step 2:** Review the code
- `routes/api.php` - API documentation
- `app/Models/License.php` - Model methods
- `app/Services/LicenseService.php` - Business logic

**Step 3:** Check the visual diagrams
- `LICENSE_SYSTEM_FLOW.md` - Flow charts

---

## ✅ Checklist for Going Live

- [ ] Create test license and verify activation
- [ ] Upload test update file and verify download
- [ ] Test force update flow
- [ ] Test optional update flow
- [ ] Configure file size limits if needed
- [ ] Set up HTTPS on production server
- [ ] Configure rate limiting
- [ ] Set up monitoring/logging
- [ ] Create admin user account
- [ ] Document internal procedures
- [ ] Train support staff
- [ ] Prepare customer communications

---

**Everything you need is ready!** 🎉

Start by creating your first license in `/admin/licenses`!

