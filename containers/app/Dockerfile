FROM node:16 AS public-builder

ARG NPM_COMMAND=build

COPY ./backend /backend
WORKDIR /backend
RUN npm install
RUN npm run ${NPM_COMMAND}

FROM php:8.0-fpm-bullseye
SHELL ["/bin/bash", "-oeux", "pipefail", "-c"]

ARG APP_ENV=example

ARG PHP_INI_PATH=./containers/app/php.ini

# timezone environment
ENV TZ=UTC \
  # locale
  LANG=en_US.UTF-8 \
  LANGUAGE=en_US:en \
  LC_ALL=en_US.UTF-8 \
  # composer environment
  COMPOSER_ALLOW_SUPERUSER=1 \
  COMPOSER_HOME=/composer

COPY --from=composer:2.5.7 /usr/bin/composer /usr/bin/composer

RUN curl -SL https://deb.nodesource.com/setup_16.x | bash

RUN apt-get update && \
  apt-get -y install git libicu-dev libonig-dev libzip-dev unzip locales nodejs netcat && \
  apt-get clean && \
  rm -rf /var/lib/apt/lists/* && \
  locale-gen en_US.UTF-8 && \
  localedef -f UTF-8 -i en_US en_US.UTF-8 && \
  mkdir /var/run/php-fpm && \
  docker-php-ext-install intl pdo_mysql zip bcmath && \
  composer config -g process-timeout 3600 && \
  composer config -g repos.packagist composer https://packagist.org

# copy PHP configuration file
COPY ${PHP_INI_PATH} /usr/local/etc/php/php.ini

# Laravel app resource
COPY ./backend /var/www
COPY --from=public-builder /backend/public /var/www/public
COPY ./backend/.env.${APP_ENV} /var/www/.env

WORKDIR /var/www

RUN mkdir -p /var/www/storage/framework/cache/data
RUN mkdir -p /var/www/storage/framework/app/cache
RUN mkdir -p /var/www/storage/framework/app/public
RUN mkdir -p /var/www/storage/framework/sessions
RUN mkdir -p /var/www/storage/framework/views
RUN chmod -R 777 /var/www/storage
RUN chmod -R 777 /var/www/bootstrap/cache

RUN composer install --no-dev

RUN if [ "$APP_ENV" = "local" ] ; then pecl install xdebug; docker-php-ext-enable xdebug; fi
