FROM php:7.4-apache

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

RUN curl -sSk https://getcomposer.org/installer | php -- --disable-tls && \
   mv composer.phar /usr/local/bin/composer
   

RUN apt-get update && apt-get install -y \
    curl \
    git \
    libbz2-dev \
    libfreetype6-dev \
    libicu-dev \
    libjpeg-dev \
    libmcrypt-dev \
    libpng-dev \
    libreadline-dev \
    libzip-dev \
    unzip \
    zip \
 && rm -rf /var/lib/apt/lists/*
 
RUN a2enmod rewrite

RUN docker-php-ext-install pdo pdo_pgsql

COPY ./public/ /var/www/html

WORKDIR /var/www/html

RUN composer update

RUN ls 



