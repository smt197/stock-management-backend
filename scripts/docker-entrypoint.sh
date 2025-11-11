#!/bin/bash
set -e

echo "🚀 Starting Laravel application..."

# Wait for database to be ready
echo "⏳ Waiting for database..."
sleep 5

# Run migrations
echo "📦 Running migrations..."
php artisan migrate --force

# Create storage link
echo "🔗 Creating storage link..."
php artisan storage:link || true

# Clear and cache config
echo "🔧 Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Seed database if needed (only on first deploy)
if [ "$SEED_DATABASE" = "true" ]; then
    echo "🌱 Seeding database..."
    php artisan db:seed --force
fi

echo "✅ Application ready!"

# Start Nginx in the background
echo "🌐 Starting Nginx..."
nginx

# Start PHP-FPM in the foreground
echo "🐘 Starting PHP-FPM..."
exec php-fpm
