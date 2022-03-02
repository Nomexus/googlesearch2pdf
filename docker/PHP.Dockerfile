FROM php:8.1.0-fpm

RUN apt-get -y update \
    && apt-get install -y libicu-dev g++ zip unzip libzip-dev

RUN docker-php-ext-configure intl

RUN docker-php-ext-install intl zip

RUN pecl install xdebug && docker-php-ext-enable xdebug

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer