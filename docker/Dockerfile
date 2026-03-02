###############################################################
# Multi-stage Dockerfile — Laravel + FrankenPHP + Bun
#
# Stages:
#   deps-php    — Composer vendor install
#   deps-js     — Bun install + Vite build
#   production  — Final slim FrankenPHP image
###############################################################

# ── PHP dependency stage ─────────────────────────────────────────
FROM composer:2 AS deps-php

WORKDIR /app

COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --no-interaction \
    --optimize-autoloader \
    --no-scripts

COPY . .

RUN composer run-script post-autoload-dump --no-interaction 2>/dev/null || true


# ── JS/CSS build stage (Bun) ─────────────────────────────────────
FROM oven/bun:1 AS deps-js

WORKDIR /app

COPY package.json bun.lockb* ./

RUN bun install --frozen-lockfile

COPY resources/   resources/
COPY vite.config.js ./
COPY public/      public/

# Copy vendor so Vite can resolve @tailwindcss/vite source paths
COPY --from=deps-php /app/vendor/laravel/framework/src/Illuminate/Pagination/resources/views \
    vendor/laravel/framework/src/Illuminate/Pagination/resources/views

RUN bun run build


# ── Production image ─────────────────────────────────────────────
FROM dunglas/frankenphp:latest-php8.4 AS production

# System dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpq-dev \
    libssl-dev \
    && docker-php-ext-install pdo_mysql pdo_pgsql opcache pcntl \
    && rm -rf /var/lib/apt/lists/*

# Enable FrankenPHP worker mode (persistent PHP workers for Octane)
ENV FRANKENPHP_CONFIG="worker ./public/index.php"
ENV APP_ENV=production
ENV LOG_CHANNEL=stderr
ENV OCTANE_SERVER=frankenphp

WORKDIR /app

# Copy application code
COPY --from=deps-php /app ./

# Copy compiled assets
COPY --from=deps-js /app/public/build ./public/build

# Entrypoint: run migrations + start Octane
RUN echo '#!/bin/sh\n\
set -e\n\
php artisan config:cache\n\
php artisan route:cache\n\
php artisan view:cache\n\
php artisan migrate --force --isolated\n\
exec php artisan octane:start --server=frankenphp --host=0.0.0.0 --port=80 --workers=auto\n\
' > /usr/local/bin/docker-entrypoint.sh && chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80 443

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
