FROM ghcr.io/artyom84783454/expelliarmus-market-php:latest

<<<<<<< Updated upstream
COPY ./crontab /etc/cron.d/laravel-cron
RUN chmod 0644 /etc/cron.d/laravel-cron \
    && crontab /etc/cron.d/laravel-cron \
    && touch /var/log/cron.log
=======
WORKDIR /var/www/expelliarmus/backend

CMD ["/bin/sh", "-c", "/usr/sbin/crond -f -l 8"]
>>>>>>> Stashed changes
