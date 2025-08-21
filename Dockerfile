# syntax=docker/dockerfile:1

FROM php:8.2-apache

RUN docker-php-ext-install pdo pdo_mysql
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

COPY ./public /var/www/html
COPY ./templates /var/www/html/templates

USER www-data