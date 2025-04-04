FROM laravelsail/php84-composer:latest

RUN apt-get clean && apt-get update

WORKDIR /var/www/html

# Copy composer files and install dependencies
COPY composer.json composer.lock ./
RUN composer install --no-scripts --no-autoloader

# Install node and npm
RUN apt-get install -y nodejs npm

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

# Expose port 80
EXPOSE 80

# Copy the entrypoint script
COPY docker-entrypoint.sh .
RUN ["chmod", "755", "docker-entrypoint.sh"]
RUN ["chmod", "+x", "docker-entrypoint.sh"]

# Start Laravel server
ENTRYPOINT ["./docker-entrypoint.sh"]