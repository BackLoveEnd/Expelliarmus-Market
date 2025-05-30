FROM node:18-alpine AS build-stage

WORKDIR /var/www/expelliarmus

RUN mkdir -p .npm && \
    npm config set cache /var/www/expelliarmus/.npm --global

COPY frontend/package*.json ./

RUN npm install

COPY frontend ./

RUN npm run build

FROM nginx:alpine-slim AS production-stage

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

COPY .docker/nginx/backend.expelliarmus.conf /etc/nginx/conf.d/
COPY .docker/nginx/frontend.expelliarmus.conf /etc/nginx/conf.d/

COPY --from=build-stage /var/www/expelliarmus /var/www/expelliarmus

CMD ["nginx", "-g", "daemon off;"]