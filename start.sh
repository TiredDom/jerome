#!/usr/bin/env bash
set -e

echo "=== ServiQ Startup Script ==="

echo "Clearing all caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo "Checking APP_KEY..."
if [ -z "$APP_KEY" ]; then
    echo "WARNING: APP_KEY not set! Generating one..."
    php artisan key:generate --force
else
    echo "APP_KEY is set"
fi

echo "Ensuring directories exist..."
mkdir -p storage/framework/{sessions,views,cache}
mkdir -p storage/logs
mkdir -p bootstrap/cache
chmod -R 777 storage bootstrap/cache

echo "Ensuring database exists..."
mkdir -p database
if [ ! -f database/database.sqlite ]; then
    touch database/database.sqlite
    chmod 777 database/database.sqlite
fi

echo "Running migrations..."
php artisan migrate --force || echo "Migration failed, continuing..."

echo "Seeding database..."
php artisan db:seed --force || echo "Seeding failed, continuing..."

echo "Starting server on port ${PORT:-8000}..."
php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
