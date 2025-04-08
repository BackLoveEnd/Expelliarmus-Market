FROM nginx:alpine-slim

# Устанавливаем рабочую директорию
WORKDIR /var/www/expelliarmus

# Передаём аргументы для UID и GID
ARG UID
ARG GID

# Определяем переменные окружения
ENV UID=${UID}
ENV GID=${GID}

# Выполняем все команды в одном слое для уменьшения количества слоёв
RUN apk update && \
    apk add --no-cache dcron && \
    mkdir -p /var/www/expelliarmus && \
    addgroup --system --gid ${GID} laravel && \
    adduser --system --uid ${UID} --ingroup laravel --shell /bin/sh --no-create-home laravel && \
    sed -i "s/user nginx/user laravel/g" /etc/nginx/nginx.conf && \
    rm /etc/nginx/conf.d/default.conf && \
    chown -R laravel:laravel /var/www/expelliarmus

# Копируем конфигурационные файлы nginx
COPY backend.expelliarmus.conf /etc/nginx/conf.d/
COPY frontend.expelliarmus.conf /etc/nginx/conf.d/

# Запускаем cron и nginx: crond работает как демон, а nginx — в переднем плане

