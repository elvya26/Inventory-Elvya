#!/bin/sh

php artisan storage:link --force 2>/dev/null || true

PORT_TO_USE=${PORT:-8000}

if [ -z "$APP_KEY" ]; then
    echo "WARNING: APP_KEY is not set! Generating temporary key..."
    php artisan key:generate --force || true
fi

if [ "${RUN_MIGRATIONS}" = "true" ]; then
    echo "Running migrations..."
    php artisan migrate --force || echo "WARNING: Database migration failed. Check DB credentials."
fi

echo "Starting server on port $PORT_TO_USE..."
exec php artisan serve --host=0.0.0.0 --port=$PORT_TO_USE

