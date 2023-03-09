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
    libpq-dev \
    unzip \
    zip \
 && rm -rf /var/lib/apt/lists/*
  
RUN a2enmod rewrite

RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql

RUN docker-php-ext-install pdo pdo_pgsql

COPY ./deploy/ /var/www/html

WORKDIR /var/www/html

RUN mkdir ./src

RUN composer install --prefer-dist
RUN composer dump-autoload --optimize


RUN composer update

# RUN php vendor/bin/doctrine orm:convert-mapping --namespace="" --force --from-database yml ./config/yaml

# RUN php vendor/bin/doctrine orm:generate-entities --generate-annotations=false --update-entities=true --generate-methods=false ./src

# RUN composer update

# Exposer le port 80 pour permettre les connexions entrantes
EXPOSE 80

# Définir l'entrée de l'application
CMD ["apache2-foreground"]





