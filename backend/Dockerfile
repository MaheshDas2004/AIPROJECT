FROM php:8.2-apache

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    && docker-php-ext-install pdo pdo_mysql

# Copy only backend files from host into the container
COPY backend/ .

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install PHP dependencies with Composer
RUN COMPOSER_ALLOW_SUPERUSER=1 composer install

# Enable Apache rewrite mod if needed
RUN a2enmod rewrite
