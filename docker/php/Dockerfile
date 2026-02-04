FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libwebp-dev \
    libzip-dev \
    unzip \
    git \
    curl \
    libonig-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install \
        gd \
        pdo_mysql \
        zip \
        exif \
        pcntl \
        bcmath \
        mbstring

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html