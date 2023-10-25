FROM php:7.4-apache

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Exposer le port 80 pour permettre les connexions entrantes
EXPOSE 80

# Définir l'entrée de l'application
CMD ["apache2-foreground"]





