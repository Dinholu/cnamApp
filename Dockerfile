FROM php:7.4-apache

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

RUN curl -sSk https://getcomposer.org/installer | php -- --disable-tls && \
   mv composer.phar /usr/local/bin/composer

RUN docker-php-ext-install pdo pdo_mysql

RUN ls /var

MKDIR /var/www/html

COPY /www/index.html /var/www/html

RUN ls /var/www/html

WORKDIR /var/www/
