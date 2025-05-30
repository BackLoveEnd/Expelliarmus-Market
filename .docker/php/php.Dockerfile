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
    make \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql pgsql opcache \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl

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

WORKDIR /var/www/expelliarmus
# Install dependencies for PHP (no dev packages)
RUN apk update && apk add --no-cache \
    imagemagick \
    postgresql-libs \
    git \
    unzip \
    zip \
    libpng \
    libjpeg-turbo \
    icu-libs \
    freetype \
    && rm -rf /var/lib/apt/lists/*
# Copy build files from the builder stage
COPY --from=builder /usr/local/lib/php/extensions /usr/local/lib/php/extensions
COPY --from=builder /usr/local/etc/php/conf.d /usr/local/etc/php/conf.d
COPY --from=builder /usr/local/include/php/ext /usr/local/include/php/ext
COPY --from=builder /usr/local/lib/php /usr/local/lib/php

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

COPY backend .

RUN composer install --optimize-autoloader

RUN composer dump-autoload

# User and group creation
RUN addgroup --system --gid ${GID} laravel \
    && adduser --system --uid ${UID} --ingroup laravel --shell /bin/sh --no-create-home laravel \
    && sed -i "s/user = www-data/user = laravel/g" /usr/local/etc/php-fpm.d/www.conf \
    && sed -i "s/group = www-data/group = laravel/g" /usr/local/etc/php-fpm.d/www.conf \
    && echo "php_admin_flag[log_errors] = on" >> /usr/local/etc/php-fpm.d/www.conf \
    && mkdir -p /nonexistent \
    && chown -R ${UID}:${GID} /nonexistent \
    && chown -R ${UID}:${GID} /var/www/expelliarmus/ \
    && chmod -R 755 /var/www/expelliarmus/storage/ \
    && chmod -R 755 /var/www/expelliarmus/bootstrap/cache/  
COPY .docker/php/php.ini /usr/local/etc/php/php.ini
USER laravel

CMD ["php-fpm", "-y", "/usr/local/etc/php-fpm.conf", "-R"]
