#!/bin/sh
set -e

# Setup permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Jika perintah pertama adalah supervisord atau php-fpm, jalankan optimasi cache
if [ "$1" = "/usr/bin/supervisord" ] || [ "$1" = "php-fpm" ]; then
    echo "⚡ Optimizing Laravel production cache..."
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
    php artisan event:cache || true
    php artisan filament:cache-components || true
fi

exec "$@"
