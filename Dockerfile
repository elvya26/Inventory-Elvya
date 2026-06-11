FROM php:8.4-cli-alpine

RUN apk add --no-cache \
    bash \
    curl-dev \
    git \
    icu-dev \
    libpq-dev \
    oniguruma-dev \
    unzip \
    zip \
    && docker-php-ext-install curl intl mbstring pdo_pgsql

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock* ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-scripts --no-autoloader

COPY . .

RUN composer dump-autoload --optimize \
    && mkdir -p storage/app/public storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN sed -i 's/\r$//' /usr/local/bin/entrypoint.sh \
    && chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 8000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
