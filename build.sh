#!/bin/bash
set -e

echo "==> Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

echo "==> Installing Node dependencies..."
npm ci

echo "==> Building assets..."
npm run build

echo "==> Caching Laravel config & routes..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Running database migrations..."
php artisan migrate --force

echo "==> Done."
