FROM php:8.3-cli-alpine

RUN apk add --no-cache \
    bash \
    git \
    icu-dev \
    libpq-dev \
    oniguruma-dev \
    unzip \
    zip \
    && docker-php-ext-install intl mbstring pdo_pgsql

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-scripts --no-autoloader

COPY . .

RUN composer dump-autoload --optimize \
    && mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
