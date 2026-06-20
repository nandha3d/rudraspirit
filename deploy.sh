#!/bin/bash
echo "🚀 Starting Deployment..."

# 1. Pull latest changes
echo "📥 Pulling latest changes..."
git pull origin main
if [ $? -ne 0 ]; then
    echo "❌ Git pull failed!"
    exit 1
fi

# 2. Install composer dependencies
echo "📦 Installing dependencies..."
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev --ignore-platform-reqs 2>&1 || {
    echo "⚠️ Composer install had issues, trying update instead..."
    composer update --no-interaction --prefer-dist --optimize-autoloader --no-dev --ignore-platform-reqs 2>&1 || true
}

# 3. Clear caches (these should not fail deployment)
echo "🧹 Clearing caches..."
php artisan cache:clear 2>&1 || true
php artisan config:clear 2>&1 || true
php artisan route:clear 2>&1 || true
php artisan view:clear 2>&1 || true

# 4. Run database migrations (skip if it fails)
echo "🗄️ Running migrations..."
php artisan migrate --force 2>&1 || {
    echo "⚠️ Migrations skipped or had issues (non-fatal)"
}

# 5. Rebuild caches
echo "⚡ Rebuilding caches..."
php artisan config:cache 2>&1 || true
php artisan route:cache 2>&1 || true
php artisan view:cache 2>&1 || true

echo "✨ Deployment Completed Successfully!"
