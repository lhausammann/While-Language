# Verwende das PHP-Image mit Apache und FPM-Unterstützung
FROM php:8.2-apache

# Installiere notwendige PHP-Erweiterungen für Symfony (z.B. PDO, opcache, etc.)
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libicu-dev \
    git \
    zip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql intl opcache \
    && rm -rf /var/lib/apt/lists/*

# Installiere Composer (Symfony benötigt Composer für die Abhängigkeiten)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Setze den Arbeitsordner im Container
WORKDIR /var/www/html

# Kopiere das Symfony-Projekt in den Container (ersetze ./src durch dein Projekt)
COPY . .

# Stelle sicher, dass die richtigen Berechtigungen gesetzt sind
RUN chown -R www-data:www-data /var/www/html/var /var/www/html/vendor

# Installiere die Composer-Abhängigkeiten
RUN composer install --no-dev --optimize-autoloader

# Exponiere den Port 80, um auf den Webserver zuzugreifen
EXPOSE 80

# Starten des Apache-Webservers
CMD ["apache2-foreground"]
