FROM ghcr.io/artyom84783454/expelliarmus-market-php:latest AS builder

FROM php:8.3-fpm-alpine AS final

WORKDIR /var/www/expelliarmus
RUN apk add --no-cache \
        libpq \
        imagemagick \
        libgomp \
        libstdc++ \
        icu-libs \
    && rm -rf /var/cache/apk/*
COPY --from=builder /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/
COPY --from=builder /usr/local/etc/php/ /usr/local/etc/php/
COPY --from=builder /var/www/expelliarmus/ ./

CMD ["php", "artisan", "queue:work", "--queue=high,low", "--sleep=3", "--tries=3"]
