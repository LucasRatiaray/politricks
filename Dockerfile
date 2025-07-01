# Multi-stage Dockerfile optimisé pour Railway
# Stage 1: Base avec PHP 8.3 + extensions
FROM php:8.3-fpm-alpine AS base

# Variables d'environnement
ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_NO_DEV=1 \
    APP_ENV=prod \
    NODE_VERSION=20

# Installation des dépendances système et extensions PHP
RUN apk add --no-cache \
    nginx \
    supervisor \
    postgresql-dev \
    libzip-dev \
    oniguruma-dev \
    curl \
    git \
    unzip \
    nodejs \
    npm \
    && docker-php-ext-install \
    pdo_pgsql \
    zip \
    opcache \
    && rm -rf /var/cache/apk/*

# Configuration OPcache pour production
RUN echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.memory_consumption=256" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.max_accelerated_files=20000" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.validate_timestamps=0" >> /usr/local/etc/php/conf.d/opcache.ini

# Configuration PHP-FPM
RUN echo "listen = 127.0.0.1:9000" >> /usr/local/etc/php-fpm.d/zz-docker.conf \
    && echo "pm.max_children = 20" >> /usr/local/etc/php-fpm.d/zz-docker.conf \
    && echo "pm.start_servers = 3" >> /usr/local/etc/php-fpm.d/zz-docker.conf \
    && echo "pm.min_spare_servers = 2" >> /usr/local/etc/php-fpm.d/zz-docker.conf \
    && echo "pm.max_spare_servers = 4" >> /usr/local/etc/php-fpm.d/zz-docker.conf

# Installation de Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Stage 2: Dependencies
FROM base AS dependencies

WORKDIR /app

# Copie des fichiers de dépendances depuis le dossier app
COPY app/composer.json app/composer.lock ./
COPY app/package.json ./

# Installation des dépendances Composer et NPM (avec dev pour fixtures)
RUN composer install --optimize-autoloader --no-scripts
RUN npm install

# Stage 3: Assets build
FROM dependencies AS assets

# Copie du code source depuis le dossier app pour build assets
COPY app/ .

# Build des assets avec Webpack Encore
RUN npm run build

# Stage 4: Production
FROM base AS production

WORKDIR /app

# Copie des dépendances installées
COPY --from=dependencies /app/vendor ./vendor
COPY --from=dependencies /app/node_modules ./node_modules

# Copie des assets buildés
COPY --from=assets /app/public/build ./public/build

# Copie du code source depuis le dossier app
COPY app/ .

# Configuration Nginx
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf

# Configuration Supervisor
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Script de démarrage avec fixtures
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh && ls -la /start.sh

# Création et permissions des dossiers Symfony + Supervisor
RUN mkdir -p /app/var/cache /app/var/log /var/log/supervisor /var/run \
    && chown -R www-data:www-data /app/var /app/public \
    && chmod -R 755 /app/var /app/public

# Nettoyage et warmup cache avec variables temporaires
RUN rm -rf /app/node_modules && echo "Node modules removed"

RUN composer dump-autoload --optimize && echo "Composer autoload dumped"

# Skip cache operations during build - will be done at runtime
RUN echo "Cache operations skipped during build - will be handled at runtime"

# Pas de script externe - commandes directes

# Port Railway
EXPOSE 8080

# Démarrage avec fixtures automatiques
ENTRYPOINT ["/start.sh"]