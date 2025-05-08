FROM ghcr.io/artyom84783454/expelliarmus-market-php:latest

WORKDIR /var/www/expelliarmus

CMD ["php", "artisan", "queue:work", "--queue=high,low", "--sleep=3", "--tries=3"]