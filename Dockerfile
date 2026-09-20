# Stage 1: Build front-end assets
FROM node:20 AS assets
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# Stage 2: PHP Application Environment (PHP 8.5)
FROM php:8.5-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    ca-certificates \
    && docker-php-ext-install pdo_mysql mbstring bcmath

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy application files and built assets
COPY . .
COPY --from=assets /app/public/build ./public/build

# Ensure required directories exist with write permissions BEFORE composer discovery runs
RUN mkdir -p bootstrap/cache storage/framework/cache/data \
    storage/framework/sessions storage/framework/views storage/logs \
    && chmod -R 777 bootstrap/cache storage

# Install PHP dependencies with clean flags
RUN composer install --no-dev --RUN composer install --nOSE 10000

CMD php artisan serve --hCMD php artisan rt 10000
