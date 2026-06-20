#!/bin/bash
# Exit immediately if any command exits with a non-zero status
set -e

echo "🚀 Starting Deployment..."

# 1. Enable Maintenance Mode
php artisan down --message="System is updating. Please try again in a few moments." || true

# 2. Pull latest changes
git pull origin main

# 3. Install composer dependencies (no development dependencies)
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev --no-audit --ignore-platform-reqs

# 4. Run database migrations
php artisan migrate --force

# 5. Clear and Cache Configuration/Routes/Views
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

php artisan config:cache
php artisan route:cache
php artisan view:cache

# 6. Disable Maintenance Mode
php artisan up

echo "✨ Deployment Completed Successfully!"
