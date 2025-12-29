FROM php:8.2-fpm

# Install system deps
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

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy hanya file composer dulu untuk caching yang benar
COPY composer.json composer.lock ./

# Install dependencies Laravel
RUN composer install --no-dev --optimize-autoloader

# Copy seluruh source code
COPY . .

# Build Tailwind / npm
RUN npm install && npm run build

# Set permission
RUN chown -R www-data:www-data /var/www

EXPOSE 9000
CMD ["php-fpm"]
