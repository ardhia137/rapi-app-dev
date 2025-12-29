FROM php:8.3-fpm

# Set working directory
WORKDIR /var/www

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nginx \
    supervisor

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Fix git safe directory
RUN git config --global --add safe.directory /var/www

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy existing application directory contents
COPY . /var/www

RUN composer install --no-interaction --optimize-autoloader --no-dev

# Copy nginx configuration
RUN echo 'events { \n\
    worker_connections 1024; \n\
} \n\
\n\
http { \n\
    include /etc/nginx/mime.types; \n\
    default_type application/octet-stream; \n\
\n\
    server { \n\
        listen 4567; \n\
        index index.php index.html; \n\
        server_name localhost; \n\
        root /var/www/public; \n\
\n\
        location / { \n\
            try_files $uri $uri/ /index.php?$query_string; \n\
        } \n\
\n\
        location ~ \.php$ { \n\
            try_files $uri =404; \n\
            fastcgi_split_path_info ^(.+\.php)(/.+)$; \n\
            fastcgi_pass 127.0.0.1:9000; \n\
            fastcgi_index index.php; \n\
            include fastcgi_params; \n\
            fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name; \n\
            fastcgi_param PATH_INFO $fastcgi_path_info; \n\
            fastcgi_read_timeout 300; \n\
            fastcgi_send_timeout 300; \n\
        } \n\
\n\
        location ~ /\.ht { \n\
            deny all; \n\
        } \n\
    } \n\
}' > /etc/nginx/nginx.conf

# Create supervisor config
RUN echo '[supervisord] \n\
nodaemon=true \n\
\n\
[program:nginx] \n\
command=/usr/sbin/nginx -g "daemon off;" \n\
autostart=true \n\
autorestart=true \n\
stdout_logfile=/dev/stdout \n\
stdout_logfile_maxbytes=0 \n\
stderr_logfile=/dev/stderr \n\
stderr_logfile_maxbytes=0 \n\
\n\
[program:php-fpm] \n\
command=/usr/local/sbin/php-fpm -F \n\
autostart=true \n\
autorestart=true \n\
stdout_logfile=/dev/stdout \n\
stdout_logfile_maxbytes=0 \n\
stderr_logfile=/dev/stderr \n\
stderr_logfile_maxbytes=0' > /etc/supervisor/conf.d/supervisord.conf

# Set permissions
RUN chown -R www-data:www-data /var/www
RUN chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Expose port 4567
EXPOSE 4567

# Start supervisor
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]