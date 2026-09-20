# Lingkungan pengembangan Reservus: PHP 8.4 + Composer + Node.js.
FROM php:8.4-cli

RUN apt-get update && apt-get install -y --no-install-recommends \
        git unzip libzip-dev libicu-dev libonig-dev libpng-dev ca-certificates curl gnupg \
    && docker-php-ext-install -j"$(nproc)" pdo_mysql zip intl bcmath gd \
    && curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y --no-install-recommends nodejs \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_HOME=/tmp/composer \
    npm_config_cache=/tmp/npm

WORKDIR /app
