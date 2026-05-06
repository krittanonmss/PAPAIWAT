#!/usr/bin/env bash
set -e

cd /var/www/html

if [ ! -f .env ]; then
    cp .env.example .env
fi

if [ ! -d vendor ]; then
    composer install
fi

if [ ! -d node_modules ]; then
    npm install
fi

if ! grep -q '^APP_KEY=base64:' .env 2>/dev/null; then
    php artisan key:generate --force
fi

php artisan storage:link >/dev/null 2>&1 || true

exec "$@"
