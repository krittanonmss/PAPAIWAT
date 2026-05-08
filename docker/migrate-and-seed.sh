#!/usr/bin/env bash
set -e

php artisan migrate --path=database/migrations/system --force
php artisan migrate --path=database/migrations/admin --force
php artisan migrate --path=database/migrations/content/categories --force
php artisan migrate --path=database/migrations/content/media --force
php artisan migrate --path=database/migrations/content --force
php artisan migrate --path=database/migrations/content/temple --force
php artisan migrate --path=database/migrations/content/article --force
php artisan migrate --path=database/migrations/content/interactions --force
php artisan migrate --path=database/migrations/content/layout --force
php artisan db:seed --force
