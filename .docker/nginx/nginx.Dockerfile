FROM nginx:latest

WORKDIR /var/www/expelliarmus

ARG UID
ARG GID

ENV UID=${UID}
ENV GID=${GID}

RUN addgroup --system --gid ${GID} laravel
RUN adduser --system --uid ${UID} --ingroup laravel --shell /bin/sh --no-create-home laravel
RUN sed -i "s/user  nginx/user laravel/g" /etc/nginx/nginx.conf

RUN rm /etc/nginx/conf.d/default.conf

COPY backend.expelliarmus.conf /etc/nginx/conf.d/
COPY frontend.expelliarmus.conf /etc/nginx/conf.d/

RUN mkdir -p /var/www/expelliarmus


