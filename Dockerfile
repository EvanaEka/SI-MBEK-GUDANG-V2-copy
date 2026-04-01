FROM php:8.2-cli

# Install GD
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd

# Install dependencies lain
RUN apt-get install -y unzip git curl

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

RUN composer install

CMD php artisan migrate --force && php artisan storage:link && php -S 0.0.0.0:${PORT:-8080} -t public