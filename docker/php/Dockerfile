FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
unzip \
git \
libpq-dev \
&& docker-php-ext-install pdo pdo_pgsql

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www