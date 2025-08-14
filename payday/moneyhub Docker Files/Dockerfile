# Dockerfile
FROM php:8.2-apache

# Install PHP extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy project files into container
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html/

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html/

# Expose port
EXPOSE 80
