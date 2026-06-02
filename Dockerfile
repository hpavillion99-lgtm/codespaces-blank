# Use an official PHP image
FROM php:8.2-fpm

# Set the working directory inside the container
WORKDIR /var/www

# Copy all your project files from your local folder into the container
COPY . /var/www

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Now run the install command
RUN composer install --no-dev --optimize-autoloader

# Expose port and start the app
EXPOSE 8080
CMD php artisan serve --host=0.0.0.0 --port=8080