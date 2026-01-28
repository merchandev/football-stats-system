FROM php:8.0-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libicu-dev \
    zlib1g-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-configure intl \
    && docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    mysqli \
    zip \
    gd \
    mbstring \
    soap \
    intl \
    && a2enmod rewrite headers \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files first for better caching
COPY composer.json* ./

# Install PHP dependencies if composer.json exists
RUN if [ -f composer.json ]; then \
    export COMPOSER_MEMORY_LIMIT=-1 && \
    composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist --ignore-platform-reqs -vvv 2>&1 || \
    (echo "Composer install failed. Trying with different flags..." && \
    composer update --no-dev --optimize-autoloader --no-interaction --prefer-dist --ignore-platform-reqs -vvv 2>&1); \
    fi

# Copy the rest of the application
COPY . /var/www/html

# Copy Apache configuration
COPY apache-config.conf /etc/apache2/sites-available/000-default.conf

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
