ARG PHP_FPM_VERSION
FROM php:${PHP_FPM_VERSION}-fpm

RUN docker-php-ext-install pdo_mysql

CMD ["php-fpm"]

EXPOSE 9000
