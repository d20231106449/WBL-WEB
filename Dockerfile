FROM node:22-bookworm AS assets

WORKDIR /app

COPY package*.json ./
RUN npm ci

COPY resources ./resources
COPY public ./public
COPY vite.config.js ./
RUN npm run build

FROM php:8.4-apache

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
ENV PORT=10000
ENV LOG_CHANNEL=stderr
ENV LOG_STACK=stderr
ENV SESSION_DRIVER=cookie
ENV CACHE_STORE=array
ENV QUEUE_CONNECTION=sync
ENV FILESYSTEM_DISK=local

RUN apt-get update \
    && apt-get install -y --no-install-recommends git unzip zip libcurl4-openssl-dev libicu-dev libonig-dev libzip-dev \
    && docker-php-ext-install curl intl mbstring zip opcache \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts

COPY . .
COPY --from=assets /app/public/build ./public/build
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf
COPY docker/render-entrypoint.sh /usr/local/bin/render-entrypoint

RUN composer dump-autoload --optimize \
    && mkdir -p storage/app/public storage/framework/cache/data storage/framework/sessions storage/framework/testing storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R ug+rwx storage bootstrap/cache \
    && chmod +x /usr/local/bin/render-entrypoint

EXPOSE 10000

ENTRYPOINT ["render-entrypoint"]
CMD ["apache2-foreground"]
