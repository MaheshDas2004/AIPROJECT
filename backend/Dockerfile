# Use PHP with Apache and Composer preinstalled
FROM php:8.2-apache

# Install dependencies for Composer and cURL
RUN apt-get update && apt-get install -y \
    unzip \
    libcurl4-openssl-dev \
    libssl-dev \
    curl \
    git \
    zip \
    && docker-php-ext-install curl

# Enable Apache mod_rewrite (optional but useful)
RUN a2enmod rewrite

# Set working directory inside container
WORKDIR /var/www/html

# Copy all backend project files (adjust path if needed)
COPY . /var/www/html

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install PHP dependencies
RUN composer install

# Set file permissions (optional, good practice)
RUN chown -R www-data:www-data /var/www/html
