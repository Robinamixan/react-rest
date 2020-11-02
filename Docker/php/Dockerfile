# See https://github.com/docker-library/php/blob/master/7.1/fpm/Dockerfile
FROM php:7.2-fpm

# issues with php-soap on php 7 fpm
RUN rm /etc/apt/preferences.d/no-debian-php

# install necessary ubuntu libraries
RUN apt-get update && apt-get install -y --no-install-recommends \
    openssl \
    git \
    libz-dev \
    unzip \
    libxml2-dev \
    php-soap \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* \

    && docker-php-ext-install zip soap pdo pdo_mysql \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer --version \

# install xdebug
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && echo "error_reporting = E_ALL" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "display_startup_errors = On" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "display_errors = On" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.remote_enable=1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.idekey=\"PHPSTORM\"" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.remote_port=9001" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.remote_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

WORKDIR /var/www/html
COPY . /var/www/html/

