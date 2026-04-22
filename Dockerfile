FROM php:8.2-apache

# Instalar extensiones necesarias
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Habilitar mod_rewrite
RUN a2enmod rewrite

# 🔥 Asegurar que solo haya un MPM activo (evita tu error)
RUN a2dismod mpm_event && a2dismod mpm_worker && a2enmod mpm_prefork

# Permitir .htaccess
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Directorio de trabajo
WORKDIR /var/www/html

# 🔥 Copiar tu proyecto al contenedor
COPY . /var/www/html/

# Permisos (importante en algunos casos)
RUN chown -R www-data:www-data /var/www/html

# Exponer puerto
EXPOSE 80