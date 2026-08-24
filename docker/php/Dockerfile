# syntax=docker/dockerfile:1
FROM php:8.3-fpm

# --- System dependencies ---
RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        unzip \
        libzip-dev \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        libicu-dev \
        libonig-dev \
        libxml2-dev \
        libcurl4-openssl-dev \
        default-mysql-client \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        intl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# --- Composer ---
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# --- php.ini tuning ---
RUN { \
        echo 'memory_limit=256M'; \
        echo 'upload_max_filesize=20M'; \
        echo 'post_max_size=21M'; \
        echo 'max_execution_time=300'; \
        echo 'date.timezone=Asia/Jakarta'; \
    } > /usr/local/etc/php/conf.d/app.ini

# --- Aplikasi ---
WORKDIR /var/www/html
COPY . .

RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader \
    && cp docker/php/entrypoint.sh /usr/local/bin/app-entrypoint.sh \
    && chmod +x /usr/local/bin/app-entrypoint.sh \
    && chown -R www-data:www-data /var/www/html \
    && su www-data -s /bin/bash -c "php artisan storage:link" \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 9000

ENTRYPOINT ["app-entrypoint.sh"]
CMD ["php-fpm"]