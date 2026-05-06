FROM php:8.4-apache

RUN apt-get update && apt-get install -y libpq-dev \
&& docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
&& docker-php-ext-install pdo pdo_pgsql pgsql

RUN chown -R www-data:www-data /var/www/html/
# COPY . /var/www/html/