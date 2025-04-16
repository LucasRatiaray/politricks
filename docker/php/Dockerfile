FROM php:8.3-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    acl \
    fcgi \
    file \
    gettext \
    git \
    gnu-libiconv \
    postgresql-dev \
    icu-dev \
    libzip-dev

# Install PHP extensions
RUN set -eux; \
    docker-php-ext-install -j$(nproc) \
        intl \
        pdo_pgsql \
        zip; \
    docker-php-ext-enable opcache

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configure PHP for development
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

# Adjust PHP settings for development
RUN echo 'memory_limit = 512M' >> "$PHP_INI_DIR/php.ini" \
    && echo 'max_execution_time = 300' >> "$PHP_INI_DIR/php.ini" \
    && echo 'upload_max_filesize = 64M' >> "$PHP_INI_DIR/php.ini" \
    && echo 'post_max_size = 64M' >> "$PHP_INI_DIR/php.ini"

WORKDIR /var/www/symfony

# Run as non-root user
RUN addgroup -g 1000 symfony && adduser -u 1000 -G symfony -D symfony
USER symfony

CMD ["php-fpm"]

EXPOSE 9000

