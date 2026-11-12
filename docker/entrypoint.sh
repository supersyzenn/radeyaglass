#!/bin/sh
set -e

# Sesuaikan port dari environment Render (default 10000)
PORT=${PORT:-10000}
sed -i "s/__PORT__/$PORT/g" /etc/nginx/nginx.conf

# Buat direktori cache jika belum ada dan pastikan permission
mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/app/public bootstrap/cache
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Buat symbolic link storage publik
php artisan storage:link --no-interaction || true

# Optimasi cache Laravel (jika APP_KEY sudah tersedia)
if [ -n "$APP_KEY" ]; then
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
fi

# Jalankan PHP-FPM di background
php-fpm -D

# Jalankan Nginx di foreground
echo "Server berjalan di port $PORT..."
exec nginx -g "daemon off;"

