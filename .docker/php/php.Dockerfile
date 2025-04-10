# Этап 1: Сборка расширений и зависимостей
FROM php:8.3-fpm AS builder

ARG UID
ARG GID

# Установка зависимостей
RUN apt-get update && apt-get install -y --no-install-recommends \
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
    libpq-dev \
    && rm -rf /var/lib/apt/lists/*

# Установка и настройка расширений PHP
RUN docker-php-ext-install pdo pdo_pgsql pgsql opcache \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl \
    && docker-php-ext-configure gd --with-freetype=/usr/include/freetype2 --with-jpeg \
    && docker-php-ext-install gd

# Установка imagick
RUN git clone https://github.com/Imagick/imagick.git --depth 1 /tmp/imagick \
    && cd /tmp/imagick && phpize && ./configure && make && make install \
    && docker-php-ext-enable imagick \
    && rm -rf /tmp/imagick

# Установка redis
RUN mkdir -p /usr/src/php/ext/redis \
    && curl -L https://github.com/phpredis/phpredis/archive/5.3.4.tar.gz | tar xvz -C /usr/src/php/ext/redis --strip 1 \
    && echo 'redis' >> /usr/src/php-available-exts \
    && docker-php-ext-install redis

# Этап 2: Финальный образ
FROM php:8.3-fpm AS final

ARG UID
ARG GID
ENV UID=${UID}
ENV GID=${GID}

WORKDIR /var/www/expelliarmus/backend

# Установка зависимостей для работы PHP (без dev-пакетов)
RUN apt-get update && apt-get install -y --no-install-recommends \
    imagemagick \
    && rm -rf /var/lib/apt/lists/*

# Копирование установленных расширений из builder-образа
COPY --from=builder /usr/local/lib/php/extensions /usr/local/lib/php/extensions
COPY --from=builder /usr/local/etc/php/conf.d /usr/local/etc/php/conf.d
COPY --from=builder /usr/local/include/php/ext /usr/local/include/php/ext
COPY --from=builder /usr/local/lib/php /usr/local/lib/php

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Настройка пользователя
RUN addgroup --system --gid ${GID} laravel \
    && adduser --system --uid ${UID} --ingroup laravel --shell /bin/sh --no-create-home laravel \
    && sed -i "s/user = www-data/user = laravel/g" /usr/local/etc/php-fpm.d/www.conf \
    && sed -i "s/group = www-data/group = laravel/g" /usr/local/etc/php-fpm.d/www.conf \
    && echo "php_admin_flag[log_errors] = on" >> /usr/local/etc/php-fpm.d/www.conf \
    && mkdir -p /nonexistent \
    && chown -R ${UID}:${GID} /nonexistent

USER laravel

CMD ["php-fpm", "-y", "/usr/local/etc/php-fpm.conf", "-R"]
