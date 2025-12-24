FROM php:8.2-apache

# 1. Installation des dépendances système et PHP
RUN apt-get update && apt-get install -y \
    libsqlite3-dev \
    && docker-php-ext-install pdo pdo_sqlite pdo_mysql

# 2. Activation du module rewrite pour les routes propres (.htaccess)
RUN a2enmod rewrite

# 3. Définition du dossier de travail
WORKDIR /var/www/html

# 4. Copie de tout le projet
COPY . .

# 5. Configuration d'Apache pour pointer sur le dossier 'public'
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 6. Correction des permissions pour l'accès aux fichiers
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html

EXPOSE 80
