FROM nginx:alpine-slim

WORKDIR /var/www/expelliarmus

ARG UID
ARG GID

ENV UID=${UID}
ENV GID=${GID}

RUN apk update && apk upgrade --no-cache \ 
    mkdir -p /var/www/expelliarmus
RUN addgroup --system --gid ${GID} laravel \
    && adduser --system --uid ${UID} --ingroup laravel --shell /bin/sh --no-create-home laravel \
    && sed -i "s/user  nginx/user laravel/g" /etc/nginx/nginx.conf\
    && rm /etc/nginx/conf.d/default.conf

COPY backend.expelliarmus.conf /etc/nginx/conf.d/
COPY frontend.expelliarmus.conf /etc/nginx/conf.d/




