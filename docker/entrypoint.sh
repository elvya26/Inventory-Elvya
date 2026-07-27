#!/bin/sh
set -e

php artisan storage:link --force 2>/dev/null || true

PORT_TO_USE=${PORT:-8000}

if [ "${RUN_MIGRATIONS}" = "true" ]; then
    php artisan migrate --force
fi

exec php artisan serve --host=0.0.0.0 --port=$PORT_TO_USE
