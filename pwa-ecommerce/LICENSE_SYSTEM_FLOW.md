# License System - Visual Flow Diagrams

## 🎨 System Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                        ADMIN PANEL (Filament)                   │
├─────────────────────────────────────────────────────────────────┤
│  ✅ Create/Edit Licenses                                        │
│  ✅ Upload Update Files (.exe, .apk, .ipa, .dmg)               │
│  ✅ Set Version Requirements                                    │
│  ✅ Enable Force Updates                                        │
│  ✅ Track Activations                                           │
└──────────────────────┬──────────────────────────────────────────┘
                       │
                       ↓
┌─────────────────────────────────────────────────────────────────┐
│                      LARAVEL BACKEND                             │
├─────────────────────────────────────────────────────────────────┤
│  📦 License Model                                               │
│     - License data & validation logic                           │
│     - Version comparison methods                                │
│     - File path management                                      │
│                                                                  │
│  🔧 License Service                                             │
│     - Business logic (activate, validate, renew)                │
│     - Activation management                                     │
│                                                                  │
│  🌐 API Controller                                              │
│     - /activate, /validate, /check-update                       │
│     - /download-update (file streaming)                         │
│                                                                  │
│  💾 Database                                                    │
│     - licenses table                                            │
│     - license_activations table                                 │
│                                                                  │
│  📂 File Storage                                                │
│     - storage/app/license-updates/                              │
│     - MyApp-v1.2.0.exe, MyApp-v1.2.0.apk, etc.                 │
└──────────────────────┬──────────────────────────────────────────┘
                       │
                       ↓
┌─────────────────────────────────────────────────────────────────┐
│                     CLIENT APPS                                  │
├─────────────────────────────────────────────────────────────────┤
│  🖥️  Windows App (.exe)                                        │
│  📱 Android App (.apk)                                          │
│  🍎 iOS App (.ipa)                                              │
│  💻 macOS App (.dmg)                                            │
│  🌍 Electron App (cross-platform)                              │
│  📲 Flutter App (mobile)                                        │
└─────────────────────────────────────────────────────────────────┘
```

---

## 🔄 License Activation Flow

```
┌──────────┐
│  CLIENT  │
│   APP    │
└────┬─────┘
     │
     │ 1. User enters license key
     │    + Generate unique machine_id
     ↓
┌──────────────────────────────────┐
│ POST /api/v1/licenses/activate   │
│                                   │
│ Request:                          │
│ {                                 │
│   "license_key": "LS-XXXX...",   │
│   "machine_id": "DESKTOP-ABC",   │
│   "machine_name": "John's PC",   │
│   "ip_address": "192.168.1.1",   │
│   "hardware_info": {...}          │
│ }                                 │
└────┬──────────────────────────────┘
     │
     │ 2. Server validates
     ↓
┌─────────────────────────────┐
│  License Service            │
│                             │
│  ✓ License exists?          │
│  ✓ Status = active?         │
│  ✓ Not expired?             │
│  ✓ Under max_activations?   │
│  ✓ Machine not activated?   │
└────┬────────────────────────┘
     │
     │ 3. Create activation record
     ↓
┌─────────────────────────────┐
│  license_activations        │
│                             │
│  INSERT:                    │
│  - license_id               │
│  - machine_id               │
│  - machine_name             │
│  - ip_address               │
│  - hardware_info            │
│  - activated_at             │
│  - status = "active"        │
└────┬────────────────────────┘
     │
     │ 4. Increment current_activations
     ↓
┌─────────────────────────────┐
│  licenses                   │
│                             │
│  UPDATE:                    │
│  current_activations += 1   │
└────┬────────────────────────┘
     │
     │ 5. Return success
     ↓
┌──────────────────────────────────┐
│ Response:                         │
│ {                                 │
│   "success": true,                │
│   "message": "Activated",         │
│   "data": {                       │
│     "license": {...},             │
│     "activation": {...}           │
│   }                               │
│ }                                 │
└────┬──────────────────────────────┘
     │
     │ 6. App saves license key locally
     ↓
┌──────────┐
│  CLIENT  │
│  Ready!  │
└──────────┘
```

---

## 🔍 Validation + Update Check Flow

```
┌──────────┐
│  CLIENT  │
│   APP    │
│ STARTUP  │
└────┬─────┘
     │
     │ 1. Load saved license_key
     │    + Get current app_version
     ↓
┌──────────────────────────────────┐
│ POST /api/v1/licenses/validate   │
│                                   │
│ Request:                          │
│ {                                 │
│   "license_key": "LS-XXXX...",   │
│   "machine_id": "DESKTOP-ABC",   │
│   "app_version": "1.0.0"         │
│ }                                 │
└────┬──────────────────────────────┘
     │
     │ 2. Server performs checks
     ↓
┌──────────────────────────────────────────┐
│  License Service - Validate              │
│                                           │
│  ✓ License exists?                        │
│  ✓ Status = active? (not revoked)        │
│  ✓ Not expired? (expires_at > now)       │
│  ✓ Machine activated?                    │
│                                           │
│  IF app_version provided:                │
│    ✓ Check version compatibility         │
│    ✓ Compare with min_app_version        │
│    ✓ Check if update available           │
│    ✓ Determine if force update required  │
└────┬──────────────────────────────────────┘
     │
     │ 3. Get version status
     ↓
┌──────────────────────────────────────────┐
│  License Model - getVersionStatus()      │
│                                           │
│  current_version = "1.0.0"               │
│  min_version = "1.0.0"                   │
│  latest_version = "1.2.0"                │
│                                           │
│  is_compatible = version_compare(...)    │
│  has_update = current < latest           │
│  requires_update = force AND incompatible│
│                                           │
│  IF update_file_path exists:             │
│    download_url = generated               │
│    file_size = from DB                   │
│    file_size_formatted = "50 MB"         │
└────┬──────────────────────────────────────┘
     │
     │ 4. Return validation result
     ↓
┌──────────────────────────────────────────┐
│ Response:                                 │
│ {                                         │
│   "success": true,                        │
│   "data": {                               │
│     "valid": true,                        │
│     "license": {...},                     │
│     "days_remaining": 365,                │
│     "version_status": {                   │
│       "current_version": "1.0.0",        │
│       "latest_version": "1.2.0",         │
│       "has_update": true,                │
│       "requires_update": false,          │
│       "download_url": "https://..."      │
│     }                                     │
│   }                                       │
│ }                                         │
└────┬──────────────────────────────────────┘
     │
     │ 5. Client handles response
     ↓
     
   ┌─────────────────────────────────┐
   │ IF requires_update = TRUE       │
   └─────────┬───────────────────────┘
             │
             ↓
   ┌─────────────────────────────────┐
   │ Show "Update Required" dialog   │
   │ - Block app usage                │
   │ - Download from download_url     │
   │ - Install update                 │
   │ - Close app                      │
   └──────────────────────────────────┘
   
   ┌─────────────────────────────────┐
   │ ELSE IF has_update = TRUE       │
   └─────────┬───────────────────────┘
             │
             ↓
   ┌─────────────────────────────────┐
   │ Show "Update Available" banner  │
   │ - User can choose to update      │
   │ - OR skip and continue           │
   └──────────────────────────────────┘
   
   ┌─────────────────────────────────┐
   │ ELSE                            │
   └─────────┬───────────────────────┘
             │
             ↓
   ┌─────────────────────────────────┐
   │ App continues normally          │
   └──────────────────────────────────┘
```

---

## 📥 File Download Flow

```
┌──────────┐
│  CLIENT  │
│   APP    │
└────┬─────┘
     │
     │ 1. Received download_url from /check-update
     │    URL: /api/v1/licenses/download-update/LS-XXXX...
     ↓
┌────────────────────────────────────────┐
│ GET /api/v1/licenses/download-update/  │
│     {licenseKey}                        │
└────┬────────────────────────────────────┘
     │
     │ 2. Controller receives request
     ↓
┌─────────────────────────────────────────┐
│  LicenseController::downloadUpdate()    │
│                                          │
│  1. Find license by key                 │
│  2. Validate:                            │
│     ✓ License exists?                   │
│     ✓ License is valid?                 │
│     ✓ update_file_path set?             │
│     ✓ File exists on disk?              │
└────┬─────────────────────────────────────┘
     │
     │ 3. Get file info
     ↓
┌─────────────────────────────────────────┐
│  License Model                           │
│                                          │
│  - getUpdateFilePath()                   │
│    → storage/app/license-updates/...    │
│                                          │
│  - Get file extension (.exe, .apk)      │
│  - Determine MIME type                  │
│  - Get file size                        │
└────┬─────────────────────────────────────┘
     │
     │ 4. Stream file to client
     ↓
┌─────────────────────────────────────────┐
│  Response Headers:                       │
│                                          │
│  Content-Type: application/x-msdownload │
│  Content-Disposition: attachment;       │
│    filename="MyApp-v1.2.0.exe"          │
│  Content-Length: 52428800               │
│  Cache-Control: no-cache                │
│                                          │
│  Body: [Binary file stream]             │
└────┬─────────────────────────────────────┘
     │
     │ 5. Client receives file
     ↓
┌──────────────────────────────┐
│  CLIENT APP                  │
│                              │
│  1. Save to temp directory   │
│  2. Verify file size         │
│  3. Launch installer         │
│  4. Close current app        │
│  5. Installer runs           │
└──────────────────────────────┘
```

---

## 🔐 Security Validation Chain

```
                    CLIENT REQUEST
                         │
                         ↓
         ┌───────────────────────────────┐
         │  1. License Key Valid?        │
         │     - Exists in database?     │
         │     - Format correct?         │
         └───────┬───────────────────────┘
                 │ ✓ YES
                 ↓
         ┌───────────────────────────────┐
         │  2. License Status Active?    │
         │     - Not "suspended"?        │
         │     - Not "revoked"?          │
         └───────┬───────────────────────┘
                 │ ✓ YES
                 ↓
         ┌───────────────────────────────┐
         │  3. License Not Expired?      │
         │     - expires_at > now()?     │
         └───────┬───────────────────────┘
                 │ ✓ YES
                 ↓
         ┌───────────────────────────────┐
         │  4. Machine Activated?        │
         │     - machine_id in            │
         │       license_activations?     │
         └───────┬───────────────────────┘
                 │ ✓ YES
                 ↓
         ┌───────────────────────────────┐
         │  5. Version Compatible?       │
         │     - app_version >=           │
         │       min_app_version?         │
         └───────┬───────────────────────┘
                 │ ✓ YES (or no requirement)
                 ↓
         ┌───────────────────────────────┐
         │   ✅ ACCESS GRANTED           │
         │   - Return license data       │
         │   - Return version status     │
         │   - Return download URL       │
         └───────────────────────────────┘

         ANY ✗ NO → Return Error Response
```

---

## 📊 Database Structure

```
┌────────────────────────────────────────────────────────────┐
│                      licenses                              │
├────────────────────────────────────────────────────────────┤
│  id (PK)                                                   │
│  license_key (unique) ────────────┐                        │
│  type                             │                        │
│  status                           │                        │
│  max_activations                  │                        │
│  current_activations              │                        │
│  issued_at                        │                        │
│  expires_at                       │                        │
│  last_renewed_at                  │                        │
│  min_app_version      ◄───── Version Control              │
│  latest_app_version   ◄───── Version Control              │
│  force_update         ◄───── Version Control              │
│  update_file_path     ◄───── File Upload                  │
│  update_file_version  ◄───── File Upload                  │
│  update_file_size     ◄───── File Upload                  │
│  metadata (JSON)                  │                        │
│  created_at                       │                        │
│  updated_at                       │                        │
│  deleted_at                       │                        │
└───────────────────────────────────┼────────────────────────┘
                                    │
                                    │ 1:N relationship
                                    │
                                    ↓
┌────────────────────────────────────────────────────────────┐
│                 license_activations                        │
├────────────────────────────────────────────────────────────┤
│  id (PK)                                                   │
│  license_id (FK) ─────────────────┘                        │
│  machine_id                                                │
│  machine_name                                              │
│  ip_address                                                │
│  hardware_info (JSON)                                      │
│  status                                                    │
│  activated_at                                              │
│  last_check_at                                             │
│  deactivated_at                                            │
│  created_at                                                │
│  updated_at                                                │
│  deleted_at                                                │
└────────────────────────────────────────────────────────────┘
```

---

## 📂 File Storage Structure

```
storage/
└── app/
    ├── license-updates/          ◄── Update files stored here
    │   ├── MyApp-v1.0.0.exe     (50 MB)
    │   ├── MyApp-v1.1.0.exe     (52 MB)
    │   ├── MyApp-v1.2.0.exe     (55 MB)
    │   ├── MyApp-v1.0.0.apk     (25 MB)
    │   ├── MyApp-v1.1.0.apk     (26 MB)
    │   └── MyApp-v1.2.0.dmg     (80 MB)
    │
    └── [other storage folders...]

NOT publicly accessible via URL
     ↓
Only accessible through API:
  GET /api/v1/licenses/download-update/{licenseKey}
     ↓
After validation:
  - License valid?
  - Not expired?
  - File exists?
     ↓
Stream file to client
```

---

## 🎯 Complete User Journey

```
┌──────────────────────────────────────────────────────────────┐
│               USER JOURNEY: From Purchase to Update          │
└──────────────────────────────────────────────────────────────┘

STEP 1: PURCHASE
    User purchases license
         ↓
    Admin generates license in Filament
         ↓
    License Key: LS-ABCD-1234-EFGH-5678
         ↓
    User receives email with key

─────────────────────────────────────────────────────────────────

STEP 2: FIRST ACTIVATION
    User downloads app (v1.0.0)
         ↓
    Installs app
         ↓
    Opens app → "Enter License Key"
         ↓
    Enters: LS-ABCD-1234-EFGH-5678
         ↓
    App calls /activate
         ↓
    Server validates & creates activation
         ↓
    App shows: "✓ Activated Successfully"
         ↓
    App continues to main interface

─────────────────────────────────────────────────────────────────

STEP 3: DAILY USAGE
    User opens app (each day)
         ↓
    App calls /validate
         ↓
    Server checks:
      ✓ License valid
      ✓ Not expired
      ✓ Version compatible
         ↓
    App continues normally

─────────────────────────────────────────────────────────────────

STEP 4: SECURITY UPDATE RELEASED
    Admin uploads MyApp-v1.2.0.exe
         ↓
    Sets:
      - min_app_version = "1.2.0"
      - force_update = true
         ↓
    Saves license

─────────────────────────────────────────────────────────────────

STEP 5: USER OPENS APP (OLD VERSION)
    User opens app v1.0.0
         ↓
    App calls /validate
         ↓
    Server responds:
      - valid = true
      - requires_update = true
      - download_url = "https://..."
         ↓
    App shows:
      ┌──────────────────────────────────┐
      │  🔒 Security Update Required     │
      │                                  │
      │  Version 1.2.0 is required.     │
      │  Download Size: 55 MB            │
      │                                  │
      │  [  Downloading...  ]           │
      └──────────────────────────────────┘
         ↓
    App downloads from download_url
         ↓
    Progress bar shows download
         ↓
    Download completes
         ↓
    App shows: "Installing update..."
         ↓
    Launches installer
         ↓
    Old app closes
         ↓
    Installer runs
         ↓
    User clicks "Install"

─────────────────────────────────────────────────────────────────

STEP 6: AFTER UPDATE
    New version (v1.2.0) installed
         ↓
    User opens app
         ↓
    App calls /validate with app_version = "1.2.0"
         ↓
    Server responds:
      - valid = true
      - is_compatible = true
      - requires_update = false
         ↓
    App continues normally
         ↓
    User sees new features!

─────────────────────────────────────────────────────────────────

✅ COMPLETE CYCLE
```

---

## 🎨 Version States Diagram

```
                    ┌─────────────────────────┐
                    │  License Configuration  │
                    └──────────┬──────────────┘
                               │
         ┌─────────────────────┼─────────────────────┐
         │                     │                     │
         ↓                     ↓                     ↓
  min_version            latest_version        force_update
   "1.0.0"                  "1.2.0"               true/false


USER'S APP VERSION: 0.9.0
├─ current < min ───────────────► is_compatible = FALSE
├─ current < latest ────────────► has_update = TRUE
└─ force_update = true ─────────► requires_update = TRUE
    
    RESULT: 🔴 MUST UPDATE (app blocked)


USER'S APP VERSION: 1.0.0
├─ current >= min ──────────────► is_compatible = TRUE
├─ current < latest ────────────► has_update = TRUE
└─ force_update = any ──────────► requires_update = FALSE
    
    RESULT: 🟡 UPDATE AVAILABLE (optional)


USER'S APP VERSION: 1.2.0
├─ current >= min ──────────────► is_compatible = TRUE
├─ current >= latest ───────────► has_update = FALSE
└─ force_update = any ──────────► requires_update = FALSE
    
    RESULT: 🟢 UP TO DATE
```

---

This visual guide should help you understand how all the components work together! 🎉

