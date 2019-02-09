# See https://github.com/docker-library/php/blob/master/7.1/fpm/Dockerfile
FROM php:7.2-fpm

ARG TIMEZONE
ARG COMPOSER_CONSUMER_KEY
ARG COMPOSER_SECRET_KEY
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
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install zip
RUN docker-php-ext-install soap

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer --version

# Set timezone
RUN ln -snf "/usr/share/zoneinfo/${TIMEZONE}" /etc/localtime && echo "${TIMEZONE}" > /etc/timezone
RUN printf '[PHP]\ndate.timezone = "%s"\n', "${TIMEZONE}" > /usr/local/etc/php/conf.d/tzone.ini
RUN "date"

# Type docker-php-ext-install to see available extensions
RUN docker-php-ext-install pdo pdo_mysql

RUN echo "{\"bitbucket-oauth\": {\"bitbucket.org\": {\"consumer-key\": \"${COMPOSER_CONSUMER_KEY}\",\"consumer-secret\": \"${COMPOSER_SECRET_KEY}\"}}}" >> /root/.composer/auth.json

# install xdebug
RUN pecl install xdebug
RUN docker-php-ext-enable xdebug
RUN echo "error_reporting = E_ALL" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "display_startup_errors = On" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "display_errors = On" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "xdebug.remote_enable=1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "xdebug.idekey=\"PHPSTORM\"" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "xdebug.remote_port=9001" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "xdebug.remote_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

WORKDIR /var/www/html
COPY . /var/www/html/

