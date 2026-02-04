FROM php:8.2-cli

WORKDIR /app

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    sqlite3 \
    libsqlite3-dev \
    nodejs \
    npm

RUN docker-php-ext-install pdo pdo_sqlite mbstring exif pcntl bcmath gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . /app

RUN composer install --optimize-autoloader --no-dev

RUN npm ci && npm run build

RUN mkdir -p database && touch database/database.sqlite && chmod 777 database/database.sqlite && chmod 777 database

RUN mkdir -p storage/framework/{sessions,views,cache} && \
    mkdir -p storage/logs && \
    mkdir -p bootstrap/cache && \
    chmod -R 777 storage bootstrap/cache

RUN php artisan key:generate --force || echo "APP_KEY will be set via environment"

RUN php artisan migrate --force || true

RUN php artisan db:seed --force || true

RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache /app/database

COPY start.sh /app/start.sh
RUN chmod +x /app/start.sh

EXPOSE 8000

CMD ["/app/start.sh"]
