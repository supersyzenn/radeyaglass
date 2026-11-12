FROM php:8.5-fpm-alpine

# Pasang dependency sistem operasi, Nginx, Node.js, dan library ekstensi PHP
RUN apk add --no-cache \
    nginx \
    curl \
    git \
    nodejs \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    postgresql-dev \
    oniguruma-dev

# Konfigurasi & pasang ekstensi PHP yang dibutuhkan Laravel
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_pgsql \
        pdo_mysql \
        mbstring \
        gd \
        zip \
        opcache

# Salin Composer resmi
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Tentukan direktori kerja
WORKDIR /var/www/html

# Salin kode aplikasi
COPY . .

# Pasang dependency PHP produksi (tanpa dev package)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Kompilasi aset frontend
RUN node scripts/build-assets.mjs

# Konfigurasi permission direktori storage dan cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Salin konfigurasi Nginx dan script entrypoint
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Render menggunakan port 10000 secara default
EXPOSE 10000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

