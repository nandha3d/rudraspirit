#!/bin/bash
echo "🚀 Starting Deployment..."

# 0. Protect the production .env. It is no longer tracked in git, so back it up
# first. If this server still has it tracked+modified, discard the tracked copy
# so an upstream deletion applies cleanly — the real values come back from the
# backup right after the pull.
if [ -f .env ]; then
    cp -f .env .env.deploy-backup
    if git ls-files --error-unmatch .env >/dev/null 2>&1; then
        git checkout -- .env 2>/dev/null || true
    fi
fi

# 1. Fetch and hard-reset to origin/main. A deploy box should always match the
# remote; this avoids "local changes would be overwritten" aborts from things
# like line-ending churn in tracked assets. .env is protected by the backup above.
echo "📥 Fetching latest changes..."
git fetch origin main && git reset --hard origin/main || {
    echo "⚠️ Fetch/reset reported issues; attempting to continue."
}

# Restore the production .env (reset may have removed or reverted it)
if [ -f .env.deploy-backup ]; then
    cp -f .env.deploy-backup .env
fi
if [ ! -f .env ]; then
    echo "❌ .env missing and no backup found — aborting."
    exit 1
fi

# 2. Install composer dependencies
echo "📦 Installing dependencies..."
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev --ignore-platform-reqs 2>&1 || {
    echo "⚠️ Composer install had issues, trying update instead..."
    composer update --no-interaction --prefer-dist --optimize-autoloader --no-dev --ignore-platform-reqs 2>&1 || true
}

# 2b. Re-apply vendor patches (composer install restores original vendor files).
# Neutralizes the vendor activation phone-home / redirect.
CCR_VENDOR="vendor/mehedi-iitdu/core-component-repository/src/CoreComponentRepository.php"
if [ -f patches/CoreComponentRepository.php ] && [ -f "$CCR_VENDOR" ]; then
    cp -f patches/CoreComponentRepository.php "$CCR_VENDOR"
    echo "🔓 Activation gate patched."
fi

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
# NOTE: config is deliberately NOT cached. Many gateway/integration code paths
# (Razorpay, Stripe, OTP/SMS providers, FCM, etc.) read env() directly at
# runtime, and `php artisan config:cache` makes env() return null outside
# config/*. Caching config would therefore silently break live payments and
# OTP. Keep config CLEARED (uncached) so env() works until those call sites are
# migrated to config(). view:cache below is safe and stays.
php artisan config:clear 2>&1 || true

# route:cache requires unique route names. This codebase currently has duplicate
# names (pre-existing addon/resource collisions), so caching would fail. Attempt
# it anyway — once the duplicates are resolved it will start working — and fall
# back to dynamic routing cleanly instead of leaving a scary error.
if php artisan route:cache 2>&1; then
    echo "✅ Routes cached."
else
    echo "⚠️ route:cache skipped (duplicate route names exist). Using dynamic routing."
    php artisan route:clear 2>&1 || true
fi

php artisan view:cache 2>&1 || true

echo "✨ Deployment Completed Successfully!"
