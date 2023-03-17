FROM php:8.2-fpm

# Update apt-get
RUN apt-get update && apt-get install -y \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    libpq-dev \
    openssl \
    libssl-dev

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip pdo_pgsql

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer --version=2.2.0

# Install Node.js and npm
RUN curl -sL https://deb.nodesource.com/setup_16.x | bash -
RUN apt-get install -y nodejs
ENV NODE_PATH=/usr/local/lib/node_modules
ENV PATH=$PATH:/usr/local/lib/node_modules/npm/bin/

# Set working directory
WORKDIR /app

# Copy Laravel files to working directory
COPY . /app

# Install dependencies
RUN composer install --no-interaction --prefer-dist --optimize-autoloader
RUN npm install && npm run build

# Set permissions for Laravel
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache

# Generate application key
RUN php artisan key:generate

# Expose port 8000 and start PHP-FPM server
EXPOSE 8000
CMD php artisan serve --host=0.0.0.0 --port=8000
