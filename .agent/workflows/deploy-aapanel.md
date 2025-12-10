---
description: Hướng dẫn deploy và update dự án lên aaPanel (Linux VPS)
---

# Deploy & Update lên aaPanel

## 🚀 DEPLOY LẦN ĐẦU

### 1. Chuẩn bị trên aaPanel

#### 1.1 Tạo Website
- Vào **Website** → **Add site**
- Nhập domain: `yourdomain.com`
- Chọn PHP version: **8.2** (hoặc cao hơn)
- Tạo database MySQL nếu cần

#### 1.2 Cài đặt PHP Extensions
Vào **App Store** → **PHP 8.2** → **Extensions** → Bật:
- `fileinfo`
- `gd` hoặc `imagick`  
- `pdo_mysql`
- `mbstring`
- `openssl`
- `bcmath`
- `exif`
- `redis` (nếu dùng)

#### 1.3 Cài đặt Composer & Node.js
- **App Store** → Cài **Composer**
- **App Store** → Cài **Node.js Manager** → Cài Node.js version 18+

---

### 2. Upload Source Code

#### Cách 1: Dùng Git (Khuyến nghị)
```bash
cd /www/wwwroot/yourdomain.com
git clone https://github.com/username/cnetpos.git .
```

#### Cách 2: Upload ZIP
- Upload file ZIP qua File Manager của aaPanel
- Giải nén vào `/www/wwwroot/yourdomain.com`

---

### 3. Cấu hình Website

#### 3.1 Đổi Document Root
Vào **Website** → Click domain → **Site directory**:
```
/www/wwwroot/yourdomain.com/public
```

#### 3.2 Cấu hình Rewrite (Nginx)
Vào **Website** → Click domain → **URL rewrite** → Chọn **laravel5** hoặc paste:
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

#### 3.3 Cấu hình SSL (Nếu cần)
Vào **Website** → Click domain → **SSL** → **Let's Encrypt** → Apply

---

### 4. Cài đặt Dependencies & Build

```bash
cd /www/wwwroot/yourdomain.com

# Copy file .env
cp .env.example .env

# Cài Composer dependencies
composer install --optimize-autoloader --no-dev

# Generate APP_KEY
php artisan key:generate

# Cài Node dependencies & Build assets
npm install
npm run build

# Tạo storage link
php artisan storage:link
```

---

### 5. Cấu hình .env

Chỉnh sửa file `.env`:
```env
APP_NAME="CnetPOS"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password

# Nếu dùng Redis
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
```

---

### 6. Chạy Migration & Seeder

```bash
cd /www/wwwroot/yourdomain.com

# Chạy migration
php artisan migrate --force
```

#### 6.1 Chạy Seeders (Deploy lần đầu - BẮT BUỘC)

```bash
# Seeder quyền và vai trò (BẮT BUỘC chạy đầu tiên)
php artisan db:seed --class=PermissionSeeder --force

# Seeder đơn vị tính
php artisan db:seed --class=UnitSeeder --force

# Seeder tags cho Work Order
php artisan db:seed --class=WorkOrderTagSeeder --force
```

#### 6.2 Seeders Tuỳ Chọn (Nếu cần data mẫu)

```bash
# Admin mặc định
php artisan db:seed --class=AdminSeeder --force

# User mặc định  
php artisan db:seed --class=UserSeeder --force

# Danh mục sản phẩm & sản phẩm mẫu
php artisan db:seed --class=CategoryProductSeeder --force

# Hoặc chạy tất cả (theo DatabaseSeeder)
php artisan db:seed --force
```

#### 📋 Danh sách Seeders có sẵn

| Seeder | Mô tả | Khi nào chạy |
|--------|-------|--------------|
| `PermissionSeeder` | Quyền & vai trò (admin, staff...) | ⚠️ BẮT BUỘC lần đầu |
| `UnitSeeder` | Đơn vị tính (cái, kg, m...) | ⚠️ BẮT BUỘC lần đầu |
| `WorkOrderTagSeeder` | Tags cho Work Order | ⚠️ BẮT BUỘC lần đầu |
| `AdminSeeder` | Tạo tài khoản admin mặc định | Tuỳ chọn |
| `UserSeeder` | Tạo user mặc định | Tuỳ chọn |
| `CategoryProductSeeder` | Danh mục + sản phẩm mẫu | Tuỳ chọn |
| `BranchSeeder` | Chi nhánh mẫu | Tuỳ chọn |

---

### 7. Phân quyền thư mục

```bash
cd /www/wwwroot/yourdomain.com

# Owner
chown -R www:www .

# Permissions
chmod -R 755 .
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

---

### 8. Cache Production

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
php artisan icons:cache
```

---

## 🔄 CẬP NHẬT KHI CÓ THAY ĐỔI

### Khi có thay đổi CODE (không có migration)

```bash
cd /www/wwwroot/yourdomain.com

# Pull code mới
git pull origin main

# Cài lại dependencies (nếu có thay đổi composer.json)
composer install --optimize-autoloader --no-dev

# Build lại assets (nếu có thay đổi frontend)
npm install
npm run build

# Clear và rebuild cache
php artisan optimize:clear
php artisan optimize
```

---

### Khi có thay đổi DATABASE (có migration mới)

> ⚠️ **QUAN TRỌNG**: Luôn backup database trước khi chạy migration trên production!

#### Bước 1: Backup Database
Trong aaPanel: **Database** → Click database → **Backup**

Hoặc qua command:
```bash
mysqldump -u username -p database_name > /www/backup/db_backup_$(date +%Y%m%d_%H%M%S).sql
```

#### Bước 2: Bật Maintenance Mode
```bash
cd /www/wwwroot/yourdomain.com
php artisan down --secret="your-secret-key"
```
> Truy cập `https://yourdomain.com/your-secret-key` để bypass maintenance mode

#### Bước 3: Pull Code & Chạy Migration
```bash
# Pull code mới
git pull origin main

# Cài dependencies
composer install --optimize-autoloader --no-dev

# Chạy migration
php artisan migrate --force

# Build assets nếu cần
npm run build
```

---

### 🔄 Khi có SEEDER MỚI (Production đã có data)

> ✅ **AN TOÀN**: Các seeder trong dự án đã dùng `firstOrCreate` - chạy lại sẽ KHÔNG duplicate data!

#### Trường hợp 1: Thêm Permission/Role mới

Khi config `system_permissions.php` có thêm module hoặc action mới:

```bash
# Chạy lại PermissionSeeder - AN TOÀN, chỉ tạo thêm permission mới
php artisan db:seed --class=PermissionSeeder --force
```

**Lưu ý**: Seeder sẽ:
- ✅ Tạo permissions mới (nếu chưa có)
- ✅ Cập nhật role permissions theo config mới
- ✅ Giữ nguyên permissions cũ đã tồn tại
- ⚠️ `syncPermissions` sẽ cập nhật lại quyền của role theo config

#### Trường hợp 2: Thêm Tags mới cho Work Order

```bash
# Chạy lại WorkOrderTagSeeder - AN TOÀN
php artisan db:seed --class=WorkOrderTagSeeder --force
```

**Lưu ý**: Seeder sẽ:
- ✅ Tạo tags mới (nếu chưa có)
- ✅ Giữ nguyên tags cũ và tags user tự tạo

#### Trường hợp 3: Thêm Unit mới

```bash
php artisan db:seed --class=UnitSeeder --force
```

#### 📋 Script Update ĐẦY ĐỦ (có cả migration + seeder)

```bash
#!/bin/bash
cd /www/wwwroot/yourdomain.com

echo "🔄 Bắt đầu update..."

# Backup database
mysqldump -u username -p'password' database_name > /www/backup/db_$(date +%Y%m%d_%H%M%S).sql

# Maintenance mode
php artisan down --secret="update-secret-2024"

# Pull code
git pull origin main

# Dependencies
composer install --optimize-autoloader --no-dev
npm install

# Migration
php artisan migrate --force

# Seeders (chỉ chạy khi cần)
php artisan db:seed --class=PermissionSeeder --force
# php artisan db:seed --class=WorkOrderTagSeeder --force
# php artisan db:seed --class=UnitSeeder --force

# Build assets
npm run build

# Clear & optimize
php artisan optimize:clear
php artisan optimize

# Fix permissions
chown -R www:www .
chmod -R 775 storage bootstrap/cache

# Enable site
php artisan up

echo "✅ Update hoàn tất!"
```

#### ⚠️ Seeders KHÔNG AN TOÀN chạy lại trên Production

Những seeder này KHÔNG dùng `firstOrCreate`, chạy lại sẽ duplicate data:
- `CategoryProductSeeder` - Tạo sản phẩm mẫu
- `PostSeeder` - Tạo bài viết mẫu
- Các seeder tạo demo data

**Nếu cần chạy lại**: Phải xoá data cũ trước hoặc sửa seeder dùng `firstOrCreate`

#### Bước 4: Clear Cache & Tắt Maintenance
```bash
# Clear cache cũ
php artisan optimize:clear

# Rebuild cache
php artisan optimize

# Tắt maintenance mode
php artisan up
```

---

### Script Update Nhanh (Tạo file `update.sh`)

Tạo file `/www/wwwroot/yourdomain.com/update.sh`:
```bash
#!/bin/bash

echo "🔄 Starting update..."

# Maintenance mode
php artisan down --secret="update-secret-2024"

# Pull latest code
git pull origin main

# Install dependencies
composer install --optimize-autoloader --no-dev

# Run migrations
php artisan migrate --force

# Build assets
npm install
npm run build

# Clear & optimize
php artisan optimize:clear
php artisan optimize

# Fix permissions
chown -R www:www .
chmod -R 775 storage bootstrap/cache

# Disable maintenance
php artisan up

echo "✅ Update completed!"
```

Chạy script:
```bash
chmod +x update.sh
./update.sh
```

---

## 🛠️ XỬ LÝ SỰ CỐ

### Lỗi Permission Denied
```bash
chown -R www:www /www/wwwroot/yourdomain.com
chmod -R 775 storage bootstrap/cache
```

### Lỗi 500 Internal Server Error
```bash
# Check Laravel log
tail -100 storage/logs/laravel.log

# Check Nginx log
tail -100 /www/wwwlogs/yourdomain.com.error.log
```

### Rollback Migration (Nếu lỗi)
```bash
# Rollback 1 batch
php artisan migrate:rollback

# Rollback cụ thể
php artisan migrate:rollback --step=1

# Restore database từ backup
mysql -u username -p database_name < /www/backup/db_backup_xxx.sql
```

### Clear All Cache
```bash
php artisan optimize:clear
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

## 📋 CHECKLIST TRƯỚC KHI DEPLOY

- [ ] Đã test đầy đủ trên local
- [ ] Đã commit và push code lên Git
- [ ] Đã backup database production
- [ ] File `.env` đã cấu hình đúng
- [ ] Đã set `APP_DEBUG=false`
- [ ] Đã chạy `npm run build`

## 📋 CHECKLIST SAU KHI DEPLOY

- [ ] Website load được bình thường
- [ ] Đăng nhập được
- [ ] Các chức năng chính hoạt động
- [ ] Không có lỗi trong `storage/logs/laravel.log`
- [ ] SSL hoạt động (nếu có)
