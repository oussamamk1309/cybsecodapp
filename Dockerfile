FROM php:8.2-apache

WORKDIR /var/www/app

RUN apt-get update && apt-get install -y --no-install-recommends unzip git zip libpq-dev libzip-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_pgsql zip \
    && a2enmod rewrite \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
 && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
 && php -r "unlink('composer-setup.php');"

COPY . .

RUN composer install --no-interaction --prefer-dist

ENV APACHE_DOCUMENT_ROOT /var/www/app/public
COPY ./docker/app.conf /etc/apache2/sites-available/000-default.conf

EXPOSE 80
CMD ["apache2-foreground"]
