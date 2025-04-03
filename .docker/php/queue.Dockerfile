FROM php:8.3-fpm-alpine

WORKDIR /var/www/expelliarmus/backend

RUN apk update && apk add --no-cache libpq-dev \
    postgresql-dev \
        && docker-php-ext-install pdo pdo_pgsql pgsql