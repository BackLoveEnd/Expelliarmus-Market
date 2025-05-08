FROM ghcr.io/artyom84783454/expelliarmus-market-php:latest

USER root

RUN apt-get update \
    && apt-get install -y cron \
    && touch /var/log/cron.log

COPY .docker/php/crontab /etc/cron.d/laravel-cron

RUN chmod 0644 /etc/cron.d/laravel-cron \
    && crontab /etc/cron.d/laravel-cron

RUN touch /var/log/cron.log

CMD ["cron", "-f", "-l", "8"]
