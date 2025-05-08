FROM php:8.2

WORKDIR /app

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libxml2-dev

RUN docker-php-ext-install pdo pdo_pgsql xml

COPY composer.json composer.lock ./

RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin \
    --filename=composer \
    --version=2.8.3

COPY . .

# Убедитесь, что .env копируется или монтируется

RUN composer install --no-dev --no-scripts

# Даем права на storage и bootstrap/cache
RUN chmod -R 775 storage bootstrap/cache || true

# Применяем миграции
RUN php artisan migrate --force || true

RUN composer dump-autoload --optimize \
    && php artisan optimize \
    && php artisan config:cache \
    && php artisan route:cache

CMD ["php", "artisan", "serve", "--host", "0.0.0.0", "--port", "8000"]
