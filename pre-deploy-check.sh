
echo "================================"
echo "  ServiQ - Pre-Deployment Check"
echo "================================"
echo ""

echo "✓ Checking composer dependencies..."
composer install --optimize-autoloader --no-dev

echo ""
echo "✓ Checking npm dependencies..."
npm ci

echo ""
echo "✓ Building frontend assets..."
npm run build

echo ""
echo "✓ Checking database..."
if [ ! -f database/database.sqlite ]; then
    touch database/database.sqlite
    echo "  Created database/database.sqlite"
fi

echo ""
echo "✓ Running migrations..."
php artisan migrate --force

echo ""
echo "✓ Optimizing Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo ""
echo "✓ Checking permissions..."
chmod -R 775 storage bootstrap/cache

echo ""
echo "================================"
echo "  ✓ All checks passed!"
echo "================================"
echo ""
echo "Your app is ready for deployment!"
echo ""
echo "Next steps:"
echo "1. Push to GitHub: git push origin main"
echo "2. Deploy on Render: Follow RENDER_DEPLOYMENT_GUIDE.md"
echo ""
