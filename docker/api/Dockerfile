FROM php:8.3-fpm-alpine AS base

RUN apk add --no-cache git unzip libzip-dev postgresql-dev libxml2-dev oniguruma-dev icu-dev \
    && docker-php-ext-install pdo pdo_pgsql opcache zip dom xml xmlwriter mbstring intl

RUN cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# dev: code is volume-mounted; php-fpm starts from base image CMD
FROM base AS dev

ARG USER_ID=1000
ARG GROUP_ID=1000

RUN apk add --no-cache $PHPIZE_DEPS linux-headers shadow \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && apk del $PHPIZE_DEPS linux-headers \
    && usermod -u ${USER_ID} www-data \
    && groupmod -g ${GROUP_ID} www-data

COPY --link \
    --from=ghcr.io/symfony-cli/symfony-cli:latest \
    /usr/local/bin/symfony /usr/local/bin/symfony

# prod: copy and install only production deps, then full source
FROM base AS prod
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts
COPY . .
RUN composer dump-autoload --optimize
