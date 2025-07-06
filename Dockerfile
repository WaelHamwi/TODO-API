FROM php:8.4-fpm

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libcurl4-openssl-dev \
    libssl-dev \
    pkg-config \
    && docker-php-ext-install pdo_mysql zip gd

# Install and enable Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Optional: install composer globally (or use composer container)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
