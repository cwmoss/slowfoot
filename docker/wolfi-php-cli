FROM ghcr.io/shyim/wolfi-php/base:latest

# Install missing extensions
RUN apk add --no-cache php-8.4 php-8.4-gd php-8.4-pdo php-8.4-pdo_sqlite \
    php-8.4-dom php-8.4-intl php-8.4-mbstring php-8.4-openssl \
    php-8.4-phar php-8.4-curl php-8.4-sockets php-8.4-xml php-8.4-xmlwriter \
    php-8.4-fileinfo php-8.4-exif

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

EXPOSE 1199