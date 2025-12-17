FROM php:8.3-fpm-alpine

# Minimal system deps + PHP extensions
RUN apk add --no-cache \
  oniguruma-dev \
  git \
  zip \
  unzip \
  && docker-php-ext-install pdo_mysql mbstring

# Get Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy composer files first to leverage cache
COPY composer.json ./

# Install dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Copy the rest of the application
COPY . .

RUN addgroup -g 1000 -S app && adduser -u 1000 -S app -G app

# Ensure permissions are correct
RUN chown -R app:app /var/www

USER app

HEALTHCHECK --interval=30s --timeout=5s --start-period=10s --retries=3 CMD php-fpm -t || exit 1
