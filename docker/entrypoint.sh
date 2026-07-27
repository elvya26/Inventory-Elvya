#!/bin/sh

mkdir -p storage/app/public storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
chmod -R 777 storage bootstrap/cache

php artisan config:clear 2>/dev/null || true
php artisan route:clear 2>/dev/null || true
php artisan cache:clear 2>/dev/null || true
php artisan storage:link --force 2>/dev/null || true

if [ -z "$APP_KEY" ]; then
    echo "WARNING: APP_KEY is not set! Generating temporary key..."
    php artisan key:generate --force || true
fi

if [ "${RUN_MIGRATIONS}" = "true" ]; then
    echo "Running migrations..."
    php artisan migrate --force || echo "WARNING: Database migration failed."
fi

PORT_TO_USE=${PORT:-8000}
echo "Configuring Nginx to listen on port $PORT_TO_USE..."
sed -i "s/LISTEN_PORT/$PORT_TO_USE/g" /etc/nginx/http.d/default.conf

echo "Starting PHP-FPM..."
php-fpm -D

echo "Starting Nginx on port $PORT_TO_USE..."
exec nginx -g 'daemon off;'
