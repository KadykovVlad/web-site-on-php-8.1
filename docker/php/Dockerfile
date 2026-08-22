FROM php:8.1-fpm-alpine

RUN apk add --no-cache oniguruma-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring opcache \
    && apk del oniguruma-dev

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

CMD ["php-fpm"]
