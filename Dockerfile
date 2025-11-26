FROM php:8.3-fpm-alpine

# Minimal system deps + PHP extensions
RUN apk add --no-cache oniguruma-dev \
  && docker-php-ext-install pdo_mysql mbstring

WORKDIR /var/www

RUN addgroup -g 1000 -S app && adduser -u 1000 -S app -G app

USER app

HEALTHCHECK --interval=30s --timeout=5s --start-period=10s --retries=3 CMD php-fpm -t || exit 1
