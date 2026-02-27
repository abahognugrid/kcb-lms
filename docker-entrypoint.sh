#!/bin/bash
set -e

# Cache config, routes, views
php artisan config:cache
php artisan route:cache
php artisan view:cache
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache
# Start supervisord
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
