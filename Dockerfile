FROM php:7.4-apache
RUN docker-php-ext-install pdo pdo_mysql && docker-php-ext-enable pdo_mysql
