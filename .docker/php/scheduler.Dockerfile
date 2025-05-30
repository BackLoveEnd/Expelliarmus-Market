FROM ghcr.io/artyom84783454/expelliarmus-market-php:latest AS builder

FROM php:8.3-fpm-alpine AS final

USER root

# Install all required dependencies and supercronic
RUN apk update && \
    apk add --no-cache \
        bash \
        curl \
        libpq \
        imagemagick \
        libgomp \
        libstdc++ \
        icu-libs && \
    curl -Lo /usr/local/bin/supercronic https://github.com/aptible/supercronic/releases/latest/download/supercronic-linux-amd64 && \
    chmod +x /usr/local/bin/supercronic && \
    rm -rf /var/cache/apk/*
WORKDIR /var/www/expelliarmus
COPY --from=builder /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/
COPY --from=builder /usr/local/etc/php/ /usr/local/etc/php/
COPY --from=builder /var/www/expelliarmus/ ./
# Copy crontab file
COPY .docker/php/crontab /etc/supercronic/laravel-cron

RUN mkdir -p /var/log && touch /var/log/cron.log

CMD ["/usr/local/bin/supercronic", "-debug", "-split-logs", "/etc/supercronic/laravel-cron"]
