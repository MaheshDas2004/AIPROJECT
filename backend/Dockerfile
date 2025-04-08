# Start with official PHP image
FROM php:8.2-apache

# Set working directory to /var/www/html
WORKDIR /var/www/html

# Copy ONLY backend files
COPY backend/ .

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Run composer install
RUN COMPOSER_ALLOW_SUPERUSER=1 composer install

# Enable Apache rewrite mod (optional, if you're using .htaccess or routes)
RUN a2enmod rewrite
