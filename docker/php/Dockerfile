FROM php:8.3-fpm-alpine

# Instalar dependencias del sistema y extensiones de PHP necesarias para Laravel y MySQL
RUN apk add --no-cache \
    bash \
    curl \
    git \
    libpng-dev \
    libxml2-dev \
    libzip-dev \
    oniguruma-dev \
    zip \
    unzip \
    mysql-client

# Instalar extensiones de PHP
RUN docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd zip

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar directorio de trabajo
WORKDIR /var/www

# Crear usuario para no correr como root
RUN addgroup -g 1000 laravel && adduser -u 1000 -G laravel -s /bin/bash -D laravel

USER laravel

EXPOSE 9000
CMD ["php-fpm"]
