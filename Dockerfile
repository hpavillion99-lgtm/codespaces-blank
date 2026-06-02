# Use a PHP image
FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    git

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql gd zip

# Set working directory
WORKDIR /var/www

# Copy existing application directory
COPY . .

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# Expose port 8080
EXPOSE 8080

# Start command
CMD php artisan serve --host=0.0.0.0 --port=8080