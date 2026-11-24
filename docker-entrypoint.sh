#!/bin/sh
set -e

if [ ! -f composer.json ]; then
    echo "==> composer.json not found, installing Laravel into /var/www/html..."
    composer create-project laravel/laravel _laravel_tmp --prefer-dist --no-interaction
    cp -a _laravel_tmp/. .
    rm -rf _laravel_tmp
fi

if [ ! -f vendor/autoload.php ]; then
    echo "==> Installing composer dependencies..."
    composer install --no-interaction --prefer-dist --optimize-autoloader
fi

if [ ! -f .env ]; then
    echo "==> Copying .env.example to .env..."
    cp .env.example .env
fi

if grep -q '^# DB_HOST=' .env; then
    sed -i \
        -e 's|^DB_CONNECTION=.*|DB_CONNECTION=pgsql|' \
        -e 's|^# DB_HOST=.*|DB_HOST=postgres|' \
        -e 's|^# DB_PORT=.*|DB_PORT=5432|' \
        -e 's|^# DB_DATABASE=.*|DB_DATABASE=laravel|' \
        -e 's|^# DB_USERNAME=.*|DB_USERNAME=laravel|' \
        -e 's|^# DB_PASSWORD=.*|DB_PASSWORD=secret|' \
        -e 's|^APP_URL=.*|APP_URL=http://localhost:8000|' \
        .env
elif ! grep -q '^DB_HOST=postgres' .env; then
    sed -i \
        -e 's|^DB_CONNECTION=.*|DB_CONNECTION=pgsql|' \
        -e 's|^DB_HOST=.*|DB_HOST=postgres|' \
        -e 's|^DB_PORT=.*|DB_PORT=5432|' \
        -e 's|^DB_DATABASE=.*|DB_DATABASE=laravel|' \
        -e 's|^DB_USERNAME=.*|DB_USERNAME=laravel|' \
        -e 's|^DB_PASSWORD=.*|DB_PASSWORD=secret|' \
        -e 's|^APP_URL=.*|APP_URL=http://localhost:8000|' \
        .env
fi

php artisan migrate --force

if ! grep -q '^APP_KEY=base64:' .env; then
    echo "==> Generating Laravel application key..."
    php artisan key:generate --force
fi

if [ -d storage ]; then
    chmod -R 777 storage bootstrap/cache
fi

exec "$@"