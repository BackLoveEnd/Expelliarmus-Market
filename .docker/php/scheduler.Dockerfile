FROM php:8.3-fpm-alpine

ADD ./crontab /etc/cron.d/laravel-cron
RUN chmod 0644 /etc/cron.d/laravel-cron \
    && crontab /etc/cron.d/laravel-cron

RUN touch /var/log/cron.log