FROM php:8.2-cli

WORKDIR /app

# 必要なパッケージ
RUN apt-get update && apt-get install -y \
    git unzip libpq-dev \
    && docker-php-ext-install pdo pdo_mysql

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# アプリコピー
COPY . .

# Laravelセットアップ
RUN composer install
RUN php artisan key:generate

EXPOSE 10000

CMD php artisan serve --host=0.0.0.0 --port=10000