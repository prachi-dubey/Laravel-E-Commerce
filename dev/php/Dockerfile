FROM php:8.4-fpm

COPY composer.lock composer.json /var/www/html/

WORKDIR /var/www/html

ENV TZ=UTC
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

RUN apt-get update && apt-get install -y \
    build-essential \
    locales \
    libzip-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    vim \
    unzip \
    git \
    curl \
    supervisor \
    libonig-dev \
    zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN curl -fsSL https://github.com/phpredis/phpredis/archive/refs/tags/6.1.0.tar.gz -o /tmp/redis.tar.gz \
    && tar -xzf /tmp/redis.tar.gz -C /tmp \
    && cd /tmp/phpredis-6.1.0 \
    && phpize \
    && ./configure \
    && make -j"$(nproc)" \
    && make install \
    && docker-php-ext-enable redis \
    && rm -rf /tmp/redis.tar.gz /tmp/phpredis-6.1.0

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath gd

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

COPY .env.example .env
COPY . /var/www/html

RUN composer install --no-interaction --prefer-dist

RUN php artisan key:generate --force \
    && php artisan route:clear \
    && php artisan config:clear

RUN usermod -u 1000 www-data && \
    chown www-data /var/log/supervisor && \
    chown www-data /var/run && \
    chown www-data /var/www/html/storage && \
    chown www-data /var/www/html/bootstrap/cache

COPY --chown=www-data:www-data . /var/www/html

USER www-data

VOLUME "/var/www/html"
