# Use the official PHP image from Docker Hub
FROM php:8.1-apache

# Enable mod_rewrite (useful for routing in PHP apps)
RUN a2enmod rewrite

# Install dependencies (e.g., Composer)
RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd

# Install Composer (dependency manager)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set the working directory in the container
WORKDIR /var/www/html

# Copy your PHP app files into the container
COPY . .

# Install PHP dependencies via Composer (if applicable)
RUN composer install

# Expose the port the app will run on
EXPOSE 80

# Start Apache server
CMD ["apache2-foreground"]
