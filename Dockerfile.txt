FROM php:8.2-apache

# Activation du module rewrite si besoin
RUN a2enmod rewrite

# Copie de tout le projet dans le dossier web d'Apache
COPY . /var/www/html/

# Exposition du port 80
EXPOSE 80