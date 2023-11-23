FROM php:7.4-apache

ENV COMPOSER_ALLOW_SUPERUSER=1

COPY ./deploy/ /var/www/html

WORKDIR /var/www/html

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf \
&& curl -sSk https://getcomposer.org/installer | php -- --disable-tls \
&& mv composer.phar /usr/local/bin/composer \
&& apt-get update && apt-get install -y \
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
&& rm -rf /var/lib/apt/lists/* \
&& a2enmod rewrite headers \
&& composer install --prefer-dist \
&& composer dump-autoload --optimize \
&& composer update

# Exposer le port 80 pour permettre les connexions entrantes
EXPOSE 80

# Définir l'entrée de l'application
CMD ["apache2-foreground"]