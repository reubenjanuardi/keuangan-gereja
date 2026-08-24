# ==========================================
# STAGE 1: Install Composer Dependencies
# ==========================================
FROM composer:2 AS vendor-builder
WORKDIR /app

ARG GITHUB_TOKEN
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_PROCESS_TIMEOUT=600

COPY composer.json composer.lock ./
RUN if [ -n "$GITHUB_TOKEN" ]; then composer config -g github-oauth.github.com "$GITHUB_TOKEN"; fi \
    && composer install \
        --no-dev \
        --no-interaction \
        --prefer-dist \
        --optimize-autoloader \
        --no-scripts \
        --ignore-platform-reqs

# ==========================================
# STAGE 2: Build Frontend Assets (Vite + Vue 3)
# ==========================================
FROM node:22-alpine AS frontend-builder
WORKDIR /app

COPY package*.json ./
RUN npm ci

# Copy vendor dari composer stage agar import Ziggy & helper Laravel dapat di-resolve saat build
COPY --from=vendor-builder /app/vendor ./vendor
COPY . .

RUN npm run build

# ==========================================
# STAGE 3: Production Runtime (PHP 8.4 + Nginx)
# ==========================================
FROM php:8.4-fpm-alpine AS runner

# Install system dependencies & PostgreSQL libraries
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    postgresql-dev \
    postgresql-client

# Install & configure PHP extensions (pdo_pgsql & pgsql untuk Supabase)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_pgsql \
        pgsql \
        bcmath \
        zip \
        gd \
        intl \
        opcache \
        exif \
        pcntl

# Production PHP settings
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && sed -i 's/upload_max_filesize = 2M/upload_max_filesize = 50M/g' "$PHP_INI_DIR/php.ini" \
    && sed -i 's/post_max_size = 8M/post_max_size = 50M/g' "$PHP_INI_DIR/php.ini" \
    && sed -i 's/memory_limit = 128M/memory_limit = 256M/g' "$PHP_INI_DIR/php.ini"

WORKDIR /var/www/html

# Buat direktori wajib Laravel & Nginx runtime
RUN mkdir -p \
    /var/www/html/storage/app/public \
    /var/www/html/storage/app/private/livewire-tmp \
    /var/www/html/storage/app/livewire-tmp \
    /var/www/html/storage/framework/cache/data \
    /var/www/html/storage/framework/sessions \
    /var/www/html/storage/framework/views \
    /var/www/html/storage/logs \
    /var/www/html/bootstrap/cache \
    /run/nginx

# Copy application source code
COPY . .

# Copy pre-built vendor and frontend assets from builder stages
COPY --from=vendor-builder /app/vendor ./vendor
COPY --from=frontend-builder /app/public/build ./public/build

# Copy configuration files & entrypoint
COPY docker/nginx/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh

RUN chmod +x /usr/local/bin/entrypoint.sh \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
