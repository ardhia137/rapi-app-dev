# Base image
FROM php:8.2-fpm

# Install system dependencies + PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    nodejs \
    npm \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

COPY . .
# Copy composer files dulu supaya caching lebih cepat
COPY composer.json composer.lock ./

# Install Laravel dependencies
RUN composer install --no-dev --optimize-autoloader

# Copy semua source code


# Build Tailwind
RUN npm install && npm run build

# Set permissions
RUN chown -R www-data:www-data /var/www

# Expose PHP-FPM port
EXPOSE 9000

CMD ["php-fpm"]
