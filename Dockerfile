FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Set ownership and permissions
RUN chown -R www-data:www-data /var/www && \
    chmod -R 775 /var/www

# Copy existing application directory
COPY --chown=www-data:www-data . /var/www

# Install dependencies
USER www-data
RUN composer install

# Create storage directory structure if it doesn't exist
RUN mkdir -p /var/www/storage/app/public

# Set storage permissions
RUN chmod -R 775 /var/www/storage \
    && chmod -R 775 /var/www/bootstrap/cache

# Remove existing storage link if it exists and create new one
RUN rm -f /var/www/public/storage \
    && php artisan storage:link

EXPOSE 9000
CMD ["php-fpm"] 