FROM php:7.4-apache

RUN set COMPOSER_ALLOW_SUPERUSER=1

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

RUN curl -sSk https://getcomposer.org/installer | php -- --disable-tls && \
   mv composer.phar /usr/local/bin/composer

RUN docker-php-ext-install pdo pdo_mysql

COPY ./public/ /var/www/html

WORKDIR /var/www/html

CMD bash -c "composer install --prefer-dist"
CMD bash -c "composer update"

