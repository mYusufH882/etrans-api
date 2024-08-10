FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo_pgsql pgsql

COPY . /var/www/html

WORKDIR /var/www/html

RUN chmod -R 775 /var/www/html/storage &&  chmod -R 775 /var/www/html/bootstrap/cache
RUN chown -R www-data:www-data /var/www/html/storage && chown -R www-data:www-data /var/www/html/bootstrap/cache
