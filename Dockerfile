# PHP-FPM untuk Laravel (backend klinik)
FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
    libzip-dev \
    libpng-dev \
    oniguruma-dev \
    icu-dev \
    linux-headers \
    && docker-php-ext-install \
    pdo_mysql \
    bcmath \
    mbstring \
    exif \
    opcache \
    zip \
    intl \
    && docker-php-ext-enable opcache

# Optional: install composer (untuk production bisa run composer install di build)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
ENV PATH="/var/www/vendor/bin:$PATH"

WORKDIR /var/www
