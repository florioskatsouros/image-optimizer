# 🎨 Image Optimizer Pro - Docker Configuration for Render.com
FROM php:8.1-apache

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libwebp-dev \
    libavif-dev \
    libzip-dev \
    unzip \
    git \
    curl \
    && rm -rf /var/lib/apt/lists/*

# Configure and install PHP extensions
RUN docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg \
    --with-webp \
    && docker-php-ext-install -j$(nproc) \
    gd \
    zip \
    fileinfo

# Enable Apache modules
RUN a2enmod rewrite headers expires deflate

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files first for better caching
COPY composer.json composer.lock ./

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Copy application files
COPY . .

# Create necessary directories and set permissions
RUN mkdir -p uploads optimized temp logs \
    && chown -R www-data:www-data uploads optimized temp logs \
    && chmod -R 755 uploads optimized temp logs \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 644 /var/www/html \
    && chmod -R 755 /var/www/html/uploads /var/www/html/optimized /var/www/html/temp /var/www/html/logs

# Copy custom Apache configuration
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

# Copy custom PHP configuration
COPY docker/php.ini /usr/local/etc/php/conf.d/app.ini

# Expose port 80
EXPOSE 80

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
    CMD curl -f http://localhost/ || exit 1

# Start Apache
CMD ["apache2-foreground"]