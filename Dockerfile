FROM php:8.2-apache

WORKDIR /var/www/html

COPY . .

# Habilita módulos de Apache y extensiones PHP para MySQL
RUN a2enmod rewrite && \
    docker-php-ext-install mysqli pdo pdo_mysql

# Opcional: Instala Composer si tu proyecto lo requiere
# COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
# RUN composer install