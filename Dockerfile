FROM php:8.2-apache

# Install system dependencies & required libraries
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libicu-dev \
    zip \
    unzip

# Install PHP extensions required by Laravel
RUN docker-php-ext-configure intl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd intl

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . /var/www/html

# Install composer dependencies ignoring platform requirements
RUN composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs

# Set permissions for Laravel storage and cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Configure Apache DocumentRoot and Port for Railway dynamic port
RUN sed -i -e 's!/var/www/html/public!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf \
    && sed -i -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Update Apache port to listen on ${PORT} or default to 80
RUN sed -i 's/80/${PORT}/g' /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf

# Enable Apache Mod Rewrite
RUN a2enmod rewrite

# Expose port
EXPOSE 80

# Custom startup script to ensure port substitution works dynamically
CMD ["apache2-foreground"]
