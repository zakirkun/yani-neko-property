#!/bin/sh
set -e

cd /var/www/html

mkdir -p storage/app/public storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

attempt=1
until php artisan migrate --force; do
    if [ "$attempt" -ge 12 ]; then
        echo "Database did not become ready after $attempt attempts." >&2
        exit 1
    fi

    echo "Database is not ready; retrying migration in 5 seconds ($attempt/12)..." >&2
    attempt=$((attempt + 1))
    sleep 5
done

php artisan db:seed --force
php artisan storage:link --force
php artisan config:clear
php artisan route:clear
php artisan view:cache

exec "$@"
