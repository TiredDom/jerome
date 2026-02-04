
echo "Starting deployment..."

composer install --optimize-autoloader --no-dev

echo "Installing npm dependencies..."
npm ci

echo "Building frontend assets..."
npm run build

echo "Creating SQLite database..."
touch database/database.sqlite
chmod 666 database/database.sqlite

echo "Running migrations..."
php artisan migrate --force --no-interaction

echo "Creating admin and test users..."
php artisan db:seed --force --no-interaction

echo "Optimizing Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Setting permissions..."
chmod -R 775 storage bootstrap/cache

echo "Deployment complete!"
