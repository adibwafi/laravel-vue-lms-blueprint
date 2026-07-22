# Use an official PHP image as the base
FROM php:8.1-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    zlib1g-dev \
    libxml2-dev \
    libzip-dev \
    libpq-dev \
    unzip \
    git \
    && docker-php-ext-install pdo_mysql pdo_pgsql pgsql zip gd

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set the working directory in the container
WORKDIR /var/www/html

# Copy your Laravel application code into the container
COPY . /var/www/html

# Disable HTTP/2 and use git source downloads for stability
ENV COMPOSER_HTTP_VERSION=1.1
ENV CURL_HTTP_VERSION=1.1

# Install PHP dependencies using Git source clones to bypass HTTP/2 zip downloads
RUN composer install --no-dev --no-interaction --prefer-source --optimize-autoloader

# Copy the startup script into the container
COPY entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/entrypoint.sh

# Set the entrypoint to execute the startup script
ENTRYPOINT ["entrypoint.sh"]