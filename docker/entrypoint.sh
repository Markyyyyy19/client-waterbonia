#!/bin/bash
set -e

# Dynamically set Apache port from Render's $PORT environment variable (default 10000)
PORT="${PORT:-10000}"
sed -i "s/Listen [0-9]*/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:[0-9]*>/<VirtualHost \*:${PORT}>/" /etc/apache2/sites-available/000-default.conf

# Ensure required Laravel storage and cache directories exist with correct permissions
mkdir -p /var/www/html/storage/framework/{sessions,views,cache,data} /var/www/html/storage/logs /var/www/html/bootstrap/cache /var/www/html/database
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

# Touch .env if missing to satisfy Artisan CLI
if [ ! -f /var/www/html/.env ]; then
    touch /var/www/html/.env
fi

# If using SQLite and database file doesn't exist yet, create it
if [ "$DB_CONNECTION" = "sqlite" ] || [ -z "$DB_CONNECTION" -a -z "$DATABASE_URL" -a -z "$DB_URL" ]; then
    if [ ! -f /var/www/html/database/database.sqlite ]; then
        touch /var/www/html/database/database.sqlite
    fi
    chown -R www-data:www-data /var/www/html/database/database.sqlite
    chmod 664 /var/www/html/database/database.sqlite
fi

# Ensure APP_KEY exists
if [ -z "$APP_KEY" ]; then
    echo "APP_KEY is not set. Generating fallback key..."
    php artisan key:generate --force
fi

# Run database migrations with retry mechanism (waits for database service to be ready)
echo "Running database migrations..."
for attempt in 1 2 3 4 5; do
    if php artisan migrate --force; then
        echo "Migrations executed successfully."
        break
    else
        echo "Migration attempt $attempt failed, retrying in 3 seconds..."
        sleep 3
    fi
done

# Run database seeder (safe & idempotent)
echo "Running database seeders..."
php artisan db:seed --force || echo "Seeder skipped or failed."

# Optimize Laravel caching for production
echo "Caching configuration, routes, and views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Application ready. Starting Apache web server on port ${PORT}..."
exec apache2-foreground
