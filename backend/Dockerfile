# Use official PHP image with Apache
FROM php:8.2-apache

# Enable mod_rewrite
RUN a2enmod rewrite

# Install curl and unzip for Composer
RUN apt-get update && apt-get install -y curl unzip libzip-dev zip && docker-php-ext-install zip

# Install Composer globally
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy all files
COPY . .

# Install PHP dependencies
RUN composer install

# Set environment variable to load .env manually
ENV PHP_ENV=production

# Expose port 80
EXPOSE 80
