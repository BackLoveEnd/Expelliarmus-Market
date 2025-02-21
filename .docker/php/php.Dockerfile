FROM php:8.3-fpm

ARG UID
ARG GID
ENV UID=${UID}
ENV GID=${GID}

RUN apt-get update && apt-get install -y \
    libpq-dev \
    git \
    zlib1g-dev \
    libicu-dev \
    libpng-dev \
    libzip-dev \
    libfreetype6-dev \
    libjpeg-dev \
    g++ \
    curl \
    zip \
    libmagickwand-dev \
    imagemagick \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/expelliarmus/backend

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN docker-php-ext-install pdo pdo_pgsql pgsql

# Broken php 8.3
#RUN pecl install imagick && docker-php-ext-enable imagick

# instead
RUN git clone https://github.com/Imagick/imagick.git --depth 1 /tmp/imagick && \
    cd /tmp/imagick && \
    phpize && \
    ./configure && \
    make && \
    make install && \
    docker-php-ext-enable imagick && \
    rm -rf /tmp/imagick

RUN docker-php-ext-install opcache

RUN docker-php-ext-configure gd \
    --with-freetype=/usr/include/freetype2 \
    --with-jpeg \
    && docker-php-ext-install gd

RUN addgroup --system --gid ${GID} laravel
RUN adduser --system --uid ${UID} --ingroup laravel --shell /bin/sh --no-create-home laravel

RUN sed -i "s/user = www-data/user = laravel/g" /usr/local/etc/php-fpm.d/www.conf
RUN sed -i "s/group = www-data/group = laravel/g" /usr/local/etc/php-fpm.d/www.conf
RUN echo "php_admin_flag[log_errors] = on" >> /usr/local/etc/php-fpm.d/www.conf

RUN mkdir -p /usr/src/php/ext/redis \
    && curl -L https://github.com/phpredis/phpredis/archive/5.3.4.tar.gz | tar xvz -C /usr/src/php/ext/redis --strip 1 \
    && echo 'redis' >> /usr/src/php-available-exts \
    && docker-php-ext-install redis

RUN chown -R ${UID}:${GID} /var/www/expelliarmus/backend
RUN chmod -R 755 /var/www/expelliarmus/backend
RUN mkdir -p /nonexistent
RUN chown -R ${UID}:${GID} /nonexistent

USER laravel

CMD ["php-fpm", "-y", "/usr/local/etc/php-fpm.conf", "-R"]
