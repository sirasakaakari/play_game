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

# .env がなければコピー
RUN if [ ! -f .env ]; then cp .env.example .env; fi

# APP_KEY を生成
RUN php artisan key:generate

# Render の割り当てポートに対応
EXPOSE $PORT

# Laravel 開発サーバーを起動
CMD php artisan serve --host=0.0.0.0 --port=$PORT