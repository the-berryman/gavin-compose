# Use the official PHP 7.4 FPM image as the base
FROM php:7.4-fpm

# Install necessary extensions, including the MySQL driver
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Copy the application code (optional, depending on your structure)
COPY ./app /var/www/html

# Set working directory
WORKDIR /var/www/html
