FROM php:8.3-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nginx \
    nodejs \
    npm

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Get Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy existing application directory
COPY . /var/www

# Copy nginx configuration
COPY docker/nginx.conf /etc/nginx/sites-available/default

# Copy environment file dari .env.docker
COPY .env.docker .env

# Generate APP_KEY jika belum ada
RUN if ! grep -q "APP_KEY=base64:" .env; then \
    php artisan key:generate; \
    fi

# Install dependencies (without --no-dev untuk include dev packages seperti Pail)
RUN composer install --no-interaction --optimize-autoloader

# Install npm dependencies and build assets
RUN npm install && npm run build

# Set permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage \
    && chmod -R 775 /var/www/bootstrap/cache

# Create startup script
RUN echo '#!/bin/bash\n\
# Fix permissions setiap start\n\
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache\n\
chmod -R 775 /var/www/storage /var/www/bootstrap/cache\n\
php artisan config:cache\n\
php artisan route:cache\n\
php artisan view:cache\n\
php-fpm -D\n\
nginx -g "daemon off;"' > /start.sh && chmod +x /start.sh

EXPOSE 4567

CMD ["/start.sh"]