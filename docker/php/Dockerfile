# Image de base : PHP 8.2 avec Apache intégré
FROM php:8.2-apache

# Active le module mod_rewrite d'Apache (utile pour les URLs propres)
RUN a2enmod rewrite

# Installe zip et git (nécessaires pour Composer)
RUN apt-get update && apt-get install -y zip unzip git libzip-dev

# Installe l'extension PHP pour MySQL et zip
RUN docker-php-ext-install pdo pdo_mysql mysqli zip

# Installe Composer (gestionnaire de dépendances PHP)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copie notre config Apache personnalisée
COPY docker/apache/httpd.conf /etc/apache2/sites-available/000-default.conf
