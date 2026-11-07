#!/bin/bash
set -e

# Ensure SQLite database exists and has write permissions
touch /var/www/html/database/database.sqlite
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

# Optimize and cache configuration
php artisan config:clear
php artisan migrate --force || true
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Render injects dynamic PORT (e.g. 10000)
PORT_TO_USE="${PORT:-80}"
sed -i "s/80/${PORT_TO_USE}/g" /etc/apache2/ports.conf /etc/apache2/sites-available/*.conf

echo "Campus Coin successfully booted on port ${PORT_TO_USE}!"

exec apache2-foreground
