<<<<<<< Updated upstream
#First stage: build the image with the required extensions
FROM php:8.3-fpm-alpine as builder
RUN apk add --no-cache libpq-dev postgresql-dev \
    && docker-php-ext-install pdo_pgsql pgsql

#Second stage: copy the extensions to a new image
FROM php:8.3-fpm-alpine
RUN apk add --no-cache libpq
COPY --from=builder /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/
COPY --from=builder /usr/local/etc/php/ /usr/local/etc/php/

WORKDIR /var/www/expelliarmus/backend
=======
FROM ghcr.io/artyom84783454/expelliarmus-market-php:latest

WORKDIR /var/www/expelliarmus/backend

CMD ["php", "artisan", "queue:work", "--queue=high,low", "--sleep=3", "--tries=3"]
>>>>>>> Stashed changes
