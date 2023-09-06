FROM php:8.2-fpm

COPY --from=composer/composer:latest-bin /composer /usr/bin/composer

RUN apt-get update && apt-get install -y \
git \
&& rm -rf /var/lib/apt/lists/* \
