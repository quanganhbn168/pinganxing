#!/bin/bash

echo "🔄 Bắt đầu update..."

# Maintenance mode
php artisan down --secret="cnet-update"

# Pull code mới
git pull origin main

# Cài dependencies
composer install --optimize-autoloader --no-dev

# Chạy migration
php artisan migrate --force

# Chạy seeders (permission, tags...)
php artisan db:seed --class=PermissionSeeder --force
php artisan db:seed --class=WorkOrderTagSeeder --force

# Build assets
npm install
npm run build

# Clear & optimize cache
php artisan optimize:clear
php artisan optimize

# Fix permissions
chown -R www:www .
chmod -R 775 storage bootstrap/cache

# Tắt maintenance
php artisan up

echo "✅ Update hoàn tất!"
