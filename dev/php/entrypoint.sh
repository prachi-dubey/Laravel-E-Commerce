#!/bin/sh
set -e

echo "Waiting for MySQL..."
until php -r '
try {
    new PDO(
        "mysql:host=" . (getenv("DB_HOST") ?: "mysql") . ";port=" . (getenv("DB_PORT") ?: "3306") . ";dbname=" . (getenv("DB_DATABASE") ?: "ecommerce_catalog"),
        getenv("DB_USERNAME") ?: "root",
        getenv("DB_PASSWORD") ?: "password"
    );
    exit(0);
} catch (Throwable $e) {
    exit(1);
}
'; do
    sleep 2
done
echo "MySQL is ready."

if ! grep -qE '^APP_KEY=.+' .env 2>/dev/null; then
    echo "Generating application key..."
    php artisan key:generate --force
fi

echo "Creating storage symlink..."
php artisan storage:link || true

echo "Running migrations and seeders..."
php artisan migrate --force --seed

echo "Clearing caches..."
php artisan route:clear
php artisan config:clear

echo "Starting PHP-FPM..."
exec docker-php-entrypoint php-fpm
