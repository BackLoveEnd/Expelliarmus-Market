FROM nginx:alpine-slim

WORKDIR /var/www/expelliarmus

ARG UID
ARG GID

ENV UID=${UID}
ENV GID=${GID}


RUN apk update && \
    apk add --no-cache dcron && \
    mkdir -p /var/www/expelliarmus && \
    addgroup --system --gid ${GID} laravel && \
    adduser --system --uid ${UID} --ingroup laravel --shell /bin/sh --no-create-home laravel && \
    sed -i "s/user nginx/user laravel/g" /etc/nginx/nginx.conf && \
    rm /etc/nginx/conf.d/default.conf && \
    chown -R laravel:laravel /var/www/expelliarmus

COPY backend.expelliarmus.conf /etc/nginx/conf.d/
COPY frontend.expelliarmus.conf /etc/nginx/conf.d/
