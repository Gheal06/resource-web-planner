FROM php:8.4-apache

RUN apt-get update && apt-get install -y libpq-dev git unzip \
&& docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
&& docker-php-ext-install pdo pdo_pgsql pgsql

# Install Composer and PHPMailer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer


RUN groupadd -g 1000 appuser && useradd -u 1000 -g 1000 -m appuser

RUN chown -R www-data:www-data /var/www/html/
# WORKDIR /var/www/html
# RUN composer require phpmailer/phpmailer
# WORKDIR /
