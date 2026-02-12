# VietSat CI/CD Deployment Guide

Hướng dẫn cấu hình CI/CD pipeline cho dự án VietSat trên Vietnix Shared Hosting.

## Mục lục

1. [Tổng quan kiến trúc](#tổng-quan-kiến-trúc)
2. [Yêu cầu](#yêu-cầu)
3. [Cấu hình GitHub Secrets](#cấu-hình-github-secrets)
4. [Tạo SSH Key](#tạo-ssh-key)
5. [Cấu hình Telegram Notification](#cấu-hình-telegram-notification)
6. [Cấu hình .env](#cấu-hình-env)
7. [Thiết lập trên Vietnix Server](#thiết-lập-trên-vietnix-server)
8. [Deploy thủ công](#deploy-thủ-công)
9. [Khắc phục sự cố](#khắc-phục-sự-cố)

---

## Thông tin Hosting của bạn

Dựa trên cấu trúc thư mục bạn đã chia sẻ:

| Thông số | Giá trị |
|----------|---------|
| **Host** | `103.200.23.222` |
| **Port** | `22` |
| **User** | `duynvcom` |
| **Project Path** | `/home/duynvcom/vietsat/pwa-ecommerce` |
| **Web Root** | `~/vietsat/pwa-ecommerce/public` |

**Cấu trúc thư mục trên server:**
```
~/ (home directory)
├── vietsat/
│   └── pwa-ecommerce/      # Code Laravel
├── public_html/            # Web root (nên symlink đến public)
├── public_html_spa/        # SPA frontend (nếu cần)
├── logs/
├── access-logs/
├── ssl/
└── mail/
```

---

## Tổng quan kiến trúc

```mermaid
flowchart LR
    A[Developer] -->|Push code| B[GitHub]
    B -->|Trigger| C[GitHub Actions]
    C -->|Build| D[Node.js + PHP]
    C -->|Test| E[PHPUnit]
    D -->|Upload| F[Artifacts]
    F -->|SSH + Rsync| G[Vietnix Server]
    G -->|Migrate| H[MySQL Database]
    G -->|Notify| I[Telegram]
```

## Yêu cầu

- Tài khoản GitHub với quyền admin repository
- Vietnix Shared Hosting có SSH access
- Telegram account (tùy chọn, cho notifications)

---

## Cấu hình GitHub Secrets

### Bước 1: Mở Repository Settings

1. Truy cập [GitHub Repository](https://github.com/your-username/vietsat)
2. Vào **Settings** → **Secrets and variables** → **Actions**

### Bước 2: Thêm Secrets

Dựa trên cấu trúc hosting của bạn:

| Secret Name | Mô tả | Giá trị của bạn |
|-------------|-------|------------------|
| `DEPLOY_HOST` | IP server Vietnix | `103.200.23.222` |
| `DEPLOY_USER` | SSH username | `duynvcom` |
| `DEPLOY_PATH` | Absolute path | `/home/duynvcom/vietsat/pwa-ecommerce` |
| `DEPLOY_PORT` | SSH port | `22` |
| `SSH_PRIVATE_KEY` | Private key SSH | `-----BEGIN OPENSSH PRIVATE KEY...` |
| `DEPLOY_URL` | Production URL | `https://vietsat.com` |
| `TELEGRAM_BOT_TOKEN` | Telegram Bot Token | `123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11` |
| `TELEGRAM_CHAT_ID` | Telegram Chat ID | `-100123456789` |

> **Lưu ý:** `DEPLOY_PATH` trỏ đến thư mục chứa code Laravel. Nếu bạn muốn deploy vào `public_html` thì đường dẫn sẽ là `/home/duynvcom/vietsat/pwa-ecommerce/public`

---

## Tạo SSH Key

### Bước 1: Tạo SSH Key (chạy trên máy local)

```bash
# Tạo SSH key mới (không đặt passphrase cho CI/CD)
ssh-keygen -t ed25519 -C "github-actions@vietsat" -f ~/.ssh/github_actions_vietsat

# Xem public key
cat ~/.ssh/github_actions_vietsat.pub
```

### Bước 2: Thêm Public Key lên Vietnix Server

```bash
# SSH vào server (lần đầu sẽ yêu cầu password)
ssh -p 22 duynvcom@103.200.23.222

# Tạo thư mục .ssh nếu chưa có
mkdir -p ~/.ssh

# Thêm public key vào authorized_keys
echo "ssh-ed25519 AAAAC3NzaC1lZDI1NTE5... github-actions@vietsat" >> ~/.ssh/authorized_keys

# Set quyền
chmod 700 ~/.ssh
chmod 600 ~/.ssh/authorized_keys

# Thoát
exit
```

### Bước 3: Test SSH Connection

```bash
# Test kết nối (không cần password nếu key đã được thêm)
ssh -i ~/.ssh/github_actions_vietsat -p 22 duynvcom@103.200.23.222 "echo 'SSH connection successful!'"

# Kết quả mong đợi:
# [duynvcom@host68 ~]$ SSH connection successful!
```

### Bước 4: Copy Private Key cho GitHub

```bash
# Xem và copy private key (bao gồm BEGIN và END)
cat ~/.ssh/github_actions_vietsat
```

**Quan trọng:** Copy toàn bộ nội dung bao gồm:
```
-----BEGIN OPENSSH PRIVATE KEY-----
...
-----END OPENSSH PRIVATE KEY-----
```

Dán vào GitHub Secret `SSH_PRIVATE_KEY`.

---

## Cấu hình Telegram Notification

### Bước 1: Tạo Telegram Bot

1. Tìm **@BotFather** trên Telegram
2. Gửi `/newbot` để tạo bot mới
3. Đặt tên bot (ví dụ: `VietSat Deploy Bot`)
4. Đặt username bot (kết thúc bằng `bot`, ví dụ: `vietsat_deploy_bot`)
5. Copy **Bot Token** (dạng: `123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11`)

### Bước 2: Lấy Chat ID

**Cách 1: Với User ID**
1. Tìm **@userinfobot** trên Telegram
2. Gửi `/start`
3. Copy **ID** (số âm, ví dụ: `-100123456789`)

**Cách 2: Với Group ID**
1. Thêm bot vào group
2. Gửi tin nhắn trong group
3. Truy cập: `https://api.telegram.org/bot<YOUR_BOT_TOKEN>/getUpdates`
4. Tìm `chat.id` trong response (bắt đầu bằng `-100`)

### Bước 3: Thêm Secrets vào GitHub

Thêm:
- `TELEGRAM_BOT_TOKEN`: Bot Token vừa tạo
- `TELEGRAM_CHAT_ID`: Chat ID vừa lấy

---

## Cấu hình .env

Tạo file `.env` trên Vietnix server với nội dung sau:

```bash
# ===========================================
# APPLICATION
# ===========================================
APP_NAME="VietSat"
APP_ENV=production
APP_KEY=base64:RANDOM_STRING_HERE
APP_DEBUG=false
APP_URL=https://vietsat.com

# ===========================================
# LOGGING
# ===========================================
LOG_CHANNEL=stack
LOG_LEVEL=error

# ===========================================
# DATABASE (Lấy từ Vietnix control panel)
# ===========================================
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_db_username
DB_PASSWORD=your_db_password

# ===========================================
# CACHE & SESSION (File-based cho shared hosting)
# ===========================================
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync

# ===========================================
# MAIL (Cấu hình qua Vietnix)
# ===========================================
# Sử dụng SMTP settings từ Vietnix control panel
MAIL_MAILER=smtp
MAIL_HOST=mail.vietsat.com
MAIL_PORT=587
MAIL_USERNAME=no-reply@vietsat.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=no-reply@vietsat.com
MAIL_FROM_NAME="VietSat"

# ===========================================
# LARAVEL REVERB (Real-time)
# ===========================================
# Tùy chọn - nếu sử dụng Laravel Reverb
BROADCAST_CONNECTION=reverb
REVERB_SERVER_ID=your-server-id
REVERB_SERVER_KEY=your-server-key
REVERB_SERVER_SECRET=your-server-secret
REVERB_HOST="0.0.0.0"
REVERB_PORT=8080
REVERB_SCHEME="http"
```

**Tạo APP_KEY:**
```bash
php artisan key:generate --show
```

---

## Thiết lập trên Vietnix Server

### Bước 1: SSH vào Server

```bash
ssh -p 22 duynvcom@103.200.23.222

# Kết quả:
# [duynvcom@host68 ~]$ ls
# access-logs  fruitocean.vn  lscache  nodejs    php.ini     public_html      public_html_test  tmp      www
# etc          logs           mail     nodevenv  public_ftp  public_html_spa  ssl               vietsat
```

### Bước 2: Kiểm tra cấu trúc thư mục dự án

```bash
# Xem cấu trúc thư mục vietsat
cd ~/vietsat
ls -la

# Kết quả mong đợi:
# drwxr-xr-x  9 duynvcom duynvcom 4096 Feb 12 10:00 .
# drwxr-xr-x  3 duynvcom duynvcom 4096 Feb 12 10:00 ..
# drwxr-xr-x  1 duynvcom duynvcom 4096 Feb 12 10:00 pwa-ecommerce
# -rw-r--r--  1 duynvcom duynvcom  220 Feb 12 10:00 .env.example
```

### Bước 3: Clone Repository

```bash
# Điều hướng đến thư mục dự án
cd ~/vietsat/pwa-ecommerce

# Clone repository (nếu chưa có)
git clone https://github.com/your-username/vietsat.git .

# Hoặc nếu đã clone trước đó:
git pull origin main

### Bước 3: Cài đặt Dependencies

```bash
composer install --no-dev --optimize-autoloader --no-interaction

# Nếu gặp lỗi memory, thử:
COMPOSER_MEMORY_LIMIT=-1 composer install --no-dev --optimize-autoloader
```

### Bước 4: Thiết lập .env

```bash
# Tạo .env từ template
cp .env.example .env

# Chỉnh sửa .env với thông tin database
nano .env

# Generate app key
php artisan key:generate --force

# Clear cache
php artisan config:clear
php artisan cache:clear
```

### Bước 5: Chạy Migrations

```bash
php artisan migrate --force
```

### Bước 6: Thiết lập Storage Link

```bash
# Tạo symlink cho storage (quan trọng cho uploads)
rm -f public/storage
php artisan storage:link
```

### Bước 7: Thiết lập Permissions

```bash
# Set quyền thư mục
chmod -R 755 storage bootstrap/cache
chmod 644 .env

# Tạo thư mục backups
mkdir -p backups
```

### Bước 8: Thiết lập Cron (cho Queue - optional)

```bash
# Mở crontab
crontab -e

# Thêm dòng sau (cho Laravel scheduler nếu cần)
* * * * * cd /home/duynvcom/vietsat/pwa-ecommerce && php artisan schedule:run >> /dev/null 2>&1
```

### Bước 9: Kiểm tra cấu hình Web Server

```bash
# Kiểm tra thư mục public_html của domain
ls -la ~/public_html/

# Nếu vietsat là domain chính, cần trỏ về thư mục pwa-ecommerce/public
# Hoặc di chuyển thư mục public ra ngoài

# Cách 1: Symlink (khuyến nghị)
# Xóa public_html và tạo symlink đến public
cd ~
rm -rf public_html
ln -s ~/vietsat/pwa-ecommerce/public public_html

# Cách 2: Di chuyển thư mục public
cd ~/vietsat/pwa-ecommerce
cp -r public/* ~/public_html/
```

---

## Deploy Thủ công

### Cách 1: Sử dụng Script

```bash
# SSH vào server
ssh -p 22 duynvcom@103.200.23.222

# Điều hướng đến thư mục dự án
cd ~/vietsat/pwa-ecommerce

# Chạy script deploy
./scripts/deploy.sh

# Với options
./scripts/deploy.sh --skip-migration    # Bỏ qua migration
./scripts/deploy.sh --backup-only      # Chỉ backup
```

### Cách 2: Git Pull trực tiếp

```bash
cd ~/vietsat/pwa-ecommerce

# Backup .env
cp .env backups/env_$(date +%Y%m%d_%H%M%S).bak

# Pull code
git fetch origin main
git reset --hard origin/main

# Cài đặt dependencies
composer install --no-dev --optimize-autoloader --no-interaction

# Chạy migrations
php artisan migrate --force

# Clear caches
php artisan config:cache
php artisan route:cache
php artisan view:clear
php artisan cache:clear

# Optimize
php artisan optimize
```

---

## Khắc phục sự cố

### Lỗi SSH Connection

```bash
# Test SSH verbose
ssh -vvv -i ~/.ssh/github_actions_vietsat -p 22 duynvcom@103.200.23.222

# Kiểm tra SSH service trên server
ssh -p 22 duynvcom@103.200.23.222 "systemctl status sshd" 2>/dev/null || echo "Shared hosting may not have systemctl"

### Lỗi Composer Memory

```bash
# Tăng memory limit
COMPOSER_MEMORY_LIMIT=-1 composer install

# Hoặc cài từng package
composer install --no-dev --prefer-dist --no-scripts
composer dump-autoload --optimize
```

### Lỗi Database Migration

```bash
# Xem lỗi chi tiết
php artisan migrate --pretend --verbose

# Rollback migration cuối cùng
php artisan migrate:rollback

# Reset và chạy lại
php artisan migrate:fresh --seed
```

### Lỗi Permissions

```bash
# Sửa permissions trên server
cd ~/vietsat/pwa-ecommerce

# Owner (shared hosting thường dùng user của bạn)
chown -R duynvcom:duynvcom .

# Folders
find . -type d -exec chmod 755 {} \;

# Files
find . -type f -exec chmod 644 {} \;

# Special
chmod 640 .env
chmod 750 artisan
chmod -R 775 storage bootstrap/cache
```

### Lỗi Telegram không gửi được

```bash
# Test API trực tiếp
curl -X POST "https://api.telegram.org/bot<YOUR_BOT_TOKEN>/sendMessage" \
    -H "Content-Type: application/json" \
    -d '{"chat_id": "<YOUR_CHAT_ID>", "text": "Test message"}'
```

### Lỗi Storage Link

```bash
# Xóa link cũ và tạo lại
rm -f public/storage
php artisan storage:link

# Nếu vẫn không hoạt động
ln -sr storage/app/public public/storage
```

### Kiểm tra Logs

```bash
# Xem Laravel logs
tail -f storage/logs/laravel.log

# Xem Nginx logs (nếu có)
tail -f /var/log/nginx/error.log
```

---

## Workflow Triggers

### Auto Deploy (Push lên main)

```yaml
# Trigger khi push lên main branch
on:
  push:
    branches: [main]
```

### Manual Deploy (Workflow Dispatch)

1. Vào **Actions** tab trên GitHub
2. Chọn **Deploy to Vietnix**
3. Click **Run workflow**
4. Chọn options:
   - Environment: production
   - Skip migration: true/false

### Rollback

```bash
# Trên server
cd ~/vietsat/pwa-ecommerce

# Xem các commit gần đây
git log --oneline -10

# Rollback về commit trước
git revert --no-commit <commit-hash>
git commit -m "Rollback to previous version"
git push origin main

# Hoặc reset hard (cẩn thận với mất dữ liệu)
git reset --hard <previous-commit-hash>
git push --force origin main
```

---

## Bảo mật

1. **Không commit .env** - Đã có trong .gitignore
2. **Sử dụng SSH key không passphrase** - Chỉ cho CI/CD
3. **Giới hạn quyền SSH user** - Chỉ cho phép git commands
4. **定期 rotation SSH key** - Thay đổi key mỗi 3-6 tháng
5. **Monitor logs** - Kiểm tra logs sau mỗi deployment

---

## Support

Nếu gặp vấn đề:
1. Kiểm tra GitHub Actions logs
2. Kiểm tra server logs: `storage/logs/laravel.log`
3. Liên hệ qua Telegram nếu đã cấu hình notification

---

**Version:** 1.0.0  
**Last Updated:** 2026-02-12

