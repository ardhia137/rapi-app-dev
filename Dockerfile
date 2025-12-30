FROM php:8.3-fpm

# Install dependencies sistem
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip

# Install PHP extensions untuk MySQL
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Copy Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Node.js & NPM (untuk npm run dev)
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - && \
    apt-get install -y nodejs

WORKDIR /var/www
COPY . .

# Install dependencies
RUN composer install
RUN npm install
RUN npm run build

# Port 4567 untuk Laravel, 5173 untuk Vite HMR
EXPOSE 4567 5173

# Jalankan server
CMD php artisan serve --host=0.0.0.0 --port=4567 & npm run build -- --host