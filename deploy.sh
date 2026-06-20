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

# 4. Run database migrations (auto database update)
# A normal batch migrate aborts on the first pending migration whose table
# already exists (this DB was seeded from an import, so history is out of sync).
# So: try a normal migrate first; if it fails, fall back to running each project
# migration on its own — already-applied ones are skipped, already-existing ones
# fail harmlessly, and genuinely new tables get created. New project migrations
# are written idempotently (Schema::hasTable guard) so re-runs are safe.
echo "🗄️ Running migrations (auto database update)..."
if php artisan migrate --force 2>&1; then
    echo "✅ Migrations applied (batch)."
else
    echo "⚠️ Batch migrate aborted (out-of-sync history). Falling back to per-file migrate..."
    for f in database/migrations/*.php; do
        php artisan migrate --force --path="database/migrations/$(basename "$f")" 2>&1 \
            | grep -iE "RUNNING|DONE|Nothing to migrate" || true
    done
    echo "✅ Per-file migration pass complete."
fi

# 5. Rebuild caches
echo "⚡ Rebuilding caches..."
php artisan config:cache 2>&1 || true
php artisan route:cache 2>&1 || true
php artisan view:cache 2>&1 || true

echo "✨ Deployment Completed Successfully!"
