FROM php:8.3-fpm-alpine

COPY .docker/php/crontab /etc/cron.d/laravel-cron
RUN chmod 0644 /etc/cron.d/laravel-cron \
    && crontab /etc/cron.d/laravel-cron \
    && touch /var/log/cron.log
# COPY backend /var/www/expelliarmus/