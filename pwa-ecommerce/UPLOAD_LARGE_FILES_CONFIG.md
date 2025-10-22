# 📤 Large File Upload Configuration Guide

## ✅ Changes Made

The system now supports uploading files up to **2GB** (increased from 500MB).

**Supported file types:**
- ✅ `.exe` (Windows executables)
- ✅ `.apk` (Android apps)
- ✅ `.ipa` (iOS apps)
- ✅ `.dmg` (macOS disk images)
- ✅ `.zip` (Compressed archives) **← NEW!**

---

## 🔧 Required PHP Configuration

To upload large files (>12MB), you need to configure PHP settings:

### Option 1: Update `php.ini` (Recommended)

**Laravel Herd Users:**
1. Open Terminal
2. Run: `php --ini` to find your php.ini location
3. Edit the file (usually at `~/.config/herd-lite/bin/php.ini` or similar)
4. Update these values:

```ini
upload_max_filesize = 2G
post_max_size = 2G
max_execution_time = 600
max_input_time = 600
memory_limit = 512M
```

5. Restart Herd/PHP-FPM:
```bash
# For Herd
herd restart php

# Or manually restart PHP-FPM
brew services restart php@8.3
```

### Option 2: Create `.user.ini` file (Project-specific)

Create a file at `/Users/adward/Herd/vietsat/pwa-ecommerce/public/.user.ini`:

```ini
upload_max_filesize = 2G
post_max_size = 2G
max_execution_time = 600
max_input_time = 600
memory_limit = 512M
```

**Note:** This only works if PHP is running in CGI/FastCGI mode (which Herd uses).

### Option 3: Use `.htaccess` (Apache only)

If using Apache, create/update `/Users/adward/Herd/vietsat/pwa-ecommerce/public/.htaccess`:

```apache
php_value upload_max_filesize 2G
php_value post_max_size 2G
php_value max_execution_time 600
php_value max_input_time 600
php_value memory_limit 512M
```

---

## 🌐 Web Server Configuration

### For Nginx (if using)

Edit your nginx config:

```nginx
http {
    client_max_body_size 2G;
    client_body_timeout 600s;
}
```

Then reload:
```bash
nginx -s reload
```

### For Apache (if using)

Add to your Apache config or .htaccess:

```apache
LimitRequestBody 2147483648  # 2GB in bytes
```

---

## ✅ Verify Configuration

### Check Current PHP Limits

Run this command:
```bash
php -i | grep -E "upload_max_filesize|post_max_size|max_execution_time|memory_limit"
```

You should see:
```
upload_max_filesize => 2G => 2G
post_max_size => 2G => 2G
max_execution_time => 600 => 600
memory_limit => 512M => 512M
```

### Test Upload via Filament

1. Go to `/admin/licenses`
2. Create or edit a license
3. In "Update File" field, try uploading a large file (e.g., 50MB+)
4. If it works → Configuration is correct! ✅
5. If it fails → Check error logs

---

## 🐛 Troubleshooting

### Error: "The file may not be greater than 12288 kilobytes"

**Cause:** PHP upload_max_filesize is still 12MB

**Solution:**
1. Update `php.ini` as shown above
2. Restart PHP/Herd
3. Verify with: `php -i | grep upload_max_filesize`

### Error: "413 Request Entity Too Large" (Nginx)

**Cause:** Nginx client_max_body_size too small

**Solution:**
```nginx
client_max_body_size 2G;
```

### Error: "POST Content-Length exceeds the limit"

**Cause:** PHP post_max_size too small

**Solution:**
Set `post_max_size = 2G` in php.ini

### Upload starts but times out

**Cause:** max_execution_time too short

**Solution:**
Set `max_execution_time = 600` (10 minutes)

---

## 📊 Recommended Settings by File Size

| Max File Size | upload_max_filesize | post_max_size | memory_limit | max_execution_time |
|---------------|---------------------|---------------|--------------|-------------------|
| 50MB | 64M | 64M | 256M | 300 |
| 100MB | 128M | 128M | 256M | 300 |
| 500MB | 512M | 512M | 512M | 600 |
| 1GB | 1G | 1G | 512M | 600 |
| 2GB | 2G | 2G | 512M | 600 |

---

## 🚀 Quick Setup for Laravel Herd

**For most users, this is the easiest:**

```bash
# 1. Find php.ini location
php --ini

# 2. Edit the file (example path, yours may differ)
nano ~/.config/herd-lite/bin/php83.ini

# 3. Add/update these lines:
upload_max_filesize = 2G
post_max_size = 2G
max_execution_time = 600
memory_limit = 512M

# 4. Save and exit (Ctrl+X, then Y, then Enter)

# 5. Restart Herd
herd restart php
```

---

## ✅ Test Your Configuration

Run this PHP script to test:

```php
<?php
// Save as test-upload.php in public/ directory

phpinfo();
```

Then visit: `http://localhost/test-upload.php`

Search for:
- `upload_max_filesize`
- `post_max_size`
- `max_execution_time`

Confirm they show your new values (2G, 2G, 600).

**Don't forget to delete `test-upload.php` after testing!**

---

## 📁 Storage Directory Permissions

Make sure the upload directory is writable:

```bash
cd /Users/adward/Herd/vietsat/pwa-ecommerce

# Create directory if not exists
mkdir -p storage/app/license-updates

# Set permissions
chmod -R 775 storage/app/license-updates
chmod -R 775 storage/logs
chmod -R 775 bootstrap/cache

# If using Herd, ownership should be correct already
# But if needed:
chown -R $(whoami):staff storage
chown -R $(whoami):staff bootstrap/cache
```

---

## 🎯 Summary

**What we changed in code:**
- ✅ Increased Filament upload limit: `512000` KB (500MB) → `2097152` KB (2GB)
- ✅ Added `.zip` file support
- ✅ Added multiple MIME types for better compatibility

**What YOU need to do:**
1. Update PHP `upload_max_filesize` and `post_max_size` to `2G`
2. Restart PHP/Herd
3. (Optional) Update web server config if using Nginx/Apache
4. Test by uploading a large file in Filament

---

## 📞 Need Help?

**Check your current PHP settings:**
```bash
php -r "echo ini_get('upload_max_filesize');"
php -r "echo ini_get('post_max_size');"
```

**View error logs:**
```bash
# Laravel logs
tail -f storage/logs/laravel.log

# PHP-FPM logs (Herd)
tail -f ~/.config/herd-lite/log/php83-fpm.log
```

---

**After configuration, you should be able to upload files up to 2GB!** 🎉

