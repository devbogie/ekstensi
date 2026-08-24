#!/bin/sh
set -e

su www-data -s /bin/bash -c "php artisan config:cache"
su www-data -s /bin/bash -c "php artisan route:cache"
su www-data -s /bin/bash -c "php artisan view:cache"

exec docker-php-entrypoint "$@"
