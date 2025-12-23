FROM php:8.2-apache

# Installation des extensions nécessaires pour SQLite (si utilisé)
RUN apt-get update && apt-get install -y libsqlite3-dev && docker-php-ext-install pdo pdo_sqlite

# Activer le module de réécriture Apache (pour les routes propres)
RUN a2enmod rewrite

# Copier les fichiers du projet dans le serveur
COPY . /var/www/html/

# Configurer Apache pour pointer vers le dossier public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Donner les permissions
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
