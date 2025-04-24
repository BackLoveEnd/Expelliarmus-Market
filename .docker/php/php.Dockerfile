# First stage: build the image with the required extensions
FROM php:8.3-fpm-alpine AS builder

ARG UID
ARG GID

# Install all required dependencies for PHP and extensions
RUN apk update && apk add --no-cache \
    git \
    zlib-dev \
    icu-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    g++ \
    curl \
    zip \
    imagemagick \
    imagemagick-dev \
    postgresql-dev \
    autoconf \
    make

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql pgsql opcache \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl

# Install GD
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd

# Install imagick
RUN git clone https://github.com/Imagick/imagick.git --depth 1 /tmp/imagick \
    && cd /tmp/imagick && phpize && ./configure && make && make install \
    && docker-php-ext-enable imagick \
    && rm -rf /tmp/imagick

# Install redis
RUN mkdir -p /usr/src/php/ext/redis \
    && curl -L https://github.com/phpredis/phpredis/archive/5.3.4.tar.gz | tar xvz -C /usr/src/php/ext/redis --strip 1 \
    && echo 'redis' >> /usr/src/php-available-exts \
    && docker-php-ext-install redis

# Final stage: copy the extensions to a new image
FROM php:8.3-fpm-alpine AS final

ARG UID
ARG GID
ENV UID=${UID}
ENV GID=${GID}

WORKDIR /var/www/expelliarmus/backend
RUN chown -R www-data:www-data /var/www/expelliarmus/backend

# Install dependencies for PHP
RUN apk update && apk add --no-cache \
    imagemagick \
    postgresql-libs \
    git \
    unzip \
    zip \
    libpng \
    libjpeg-turbo \
    icu-libs \
    freetype

# Copy build files from the builder stage
COPY --from=builder /usr/local/lib/php/extensions /usr/local/lib/php/extensions
COPY --from=builder /usr/local/etc/php/conf.d /usr/local/etc/php/conf.d
COPY --from=builder /usr/local/include/php/ext /usr/local/include/php/ext
COPY --from=builder /usr/local/lib/php /usr/local/lib/php

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

COPY backend/ ./
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# User and group creation
RUN addgroup -S -g ${GID} laravel \
    && adduser -S -u ${UID} -G laravel -s /bin/sh laravel \
    && sed -i "s/user = www-data/user = laravel/g" /usr/local/etc/php-fpm.d/www.conf \
    && sed -i "s/group = www-data/group = laravel/g" /usr/local/etc/php-fpm.d/www.conf \
    && echo "php_admin_flag[log_errors] = on" >> /usr/local/etc/php-fpm.d/www.conf \
    && mkdir -p /nonexistent \
    && chown -R ${UID}:${GID} /nonexistent

USER laravel

CMD ["php-fpm", "-y", "/usr/local/etc/php-fpm.conf", "-R"]
