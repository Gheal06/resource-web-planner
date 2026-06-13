FROM php:8.4-apache

RUN apt-get update && apt-get install -y libpq-dev git unzip \
&& docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
&& docker-php-ext-install pdo pdo_pgsql pgsql

RUN apt install postgresql-client -y
RUN apt install python3 python3-pip -y
RUN apt install python3-psycopg2 -y

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libxml2-dev \
    zip \
    unzip

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd zip xml

# Install Composer and PHPMailer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer


RUN groupadd -g 1000 appuser && useradd -u 1000 -g 1000 -m appuser

RUN chown -R www-data:www-data /var/www/html/
COPY php_dir/composer.json /var/www/html/
COPY php_dir/composer.lock /var/www/html/
RUN cd /var/www/html && composer install
# WORKDIR /var/www/html
# RUN composer require phpmailer/phpmailer
# WORKDIR /
