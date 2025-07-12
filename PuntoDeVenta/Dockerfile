# Imagen base con PHP y Apache
FROM php:8.1-apache

# Instalar extensiones necesarias
RUN apt-get update \
    && apt-get install -y git unzip zip \
    && docker-php-ext-install mysqli pdo pdo_mysql

# Instalar Composer globalmente
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Instalar y habilitar Xdebug
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# Configuración básica de Xdebug para desarrollo
RUN echo "\
[xdebug]\n\
xdebug.mode=develop,debug\n\
xdebug.start_with_request=yes\n\
xdebug.client_host=host.docker.internal\n\
xdebug.client_port=9003\n\
" > /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Habilitar mod_rewrite para .htaccess
RUN a2enmod rewrite

# Copiar el código fuente al contenedor
COPY . /var/www/html/

# Ajustar permisos (opcional, según entorno)
RUN chown -R www-data:www-data /var/www/html

# Exponer el puerto 80 y el de Xdebug
EXPOSE 80 9003 