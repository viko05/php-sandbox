FROM php:8.2-fpm

COPY --from=composer/composer:latest-bin /composer /usr/bin/composer

RUN apt-get update && apt-get install -y \
git \
libzip-dev \
&& docker-php-ext-install zip \
&& rm -rf /var/lib/apt/lists/* \
