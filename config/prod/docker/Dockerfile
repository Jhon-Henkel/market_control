FROM composer AS composer-php

FROM php:8.4-apache

COPY . .
WORKDIR /var/www/html/backend

# copy composer
COPY --from=composer-php /usr/bin/composer /usr/bin/composer

# installing zip
RUN apt-get update && apt-get install -y zlib1g-dev libzip-dev unzip
RUN docker-php-ext-install zip

RUN apt-get install php-gd
RUN apt-get install libpng-dev libjpeg-dev


# installing bcmath
RUN docker-php-ext-install bcmath

# instaling pdo
RUN docker-php-ext-install mysqli pdo pdo_mysql

# modifying apache
RUN a2enmod rewrite
RUN addgroup --gid 1000 appuser; \
    adduser --uid 1000 --gid 1000 --disabled-password appuser; \
    adduser www-data appuser; \
    sed -i '/<Directory \/var\/www\/backend\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf; \
    sed -i 's|/var/www/html|/var/www/html/backend/public|' /etc/apache2/sites-available/000-default.conf; \
    chown -R www-data:www-data /var/www/html/backend/public; \
    service apache2 restart;

# installing wget
RUN apt-get install -y wget

# installing dockerize
ENV DOCKERIZE_VERSION v0.6.1
RUN wget https://github.com/jwilder/dockerize/releases/download/$DOCKERIZE_VERSION/dockerize-linux-amd64-$DOCKERIZE_VERSION.tar.gz \
    && tar -C /usr/local/bin -xzvf dockerize-linux-amd64-$DOCKERIZE_VERSION.tar.gz \
    && rm dockerize-linux-amd64-$DOCKERIZE_VERSION.tar.gz