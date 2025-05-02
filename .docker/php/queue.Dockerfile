#First stage: build the image with the required extensions
FROM php:8.3-fpm-alpine AS builder
RUN apk add --no-cache libpq-dev postgresql-dev icu-dev\
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl \
    && docker-php-ext-install pdo_pgsql pgsql
#Second stage: copy the extensions to a new image
FROM php:8.3-fpm-alpine
RUN apk add --no-cache libpq \
        libpq \
        icu-libs
COPY --from=builder /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/
COPY --from=builder /usr/local/etc/php/ /usr/local/etc/php/

WORKDIR /var/www/expelliarmus/backend