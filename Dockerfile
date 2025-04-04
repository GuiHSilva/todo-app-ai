FROM composer:latest

WORKDIR /var/www/html

# Copy composer files and install dependencies
COPY composer.json composer.lock ./
RUN composer install --no-scripts --no-autoloader

# Copy the rest of the application
COPY . .

RUN cp .env.example .env

# Generate optimized autoloader
RUN composer dump-autoload --optimize

RUN php artisan key:generate

RUN php artisan migrate

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage

WORKDIR /var/www/html

# Expose port 80
EXPOSE 80

# Start Laravel server
CMD ["php", "artisan", "serve", "--port=80", "--host=0.0.0.0"]