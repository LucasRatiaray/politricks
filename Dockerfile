# Multi-stage Dockerfile optimisé pour Railway
# Stage 1: Base avec PHP 8.3 + extensions (utilise une image pré-construite)
FROM thecodingmachine/php:8.3-v4-fpm-alpine AS base

# Variables d'environnement
ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_NO_DEV=1 \
    APP_ENV=prod \
    NODE_VERSION=20 \
    PHP_EXTENSIONS="pdo_pgsql zip opcache intl"

# Installation des dépendances système seulement
RUN apk add --no-cache \
    nginx \
    supervisor \
    nodejs \
    npm \
    && rm -rf /var/cache/apk/*

# Configuration PHP optimisée pour Railway
ENV PHP_INI_OPCACHE__ENABLE=1 \
    PHP_INI_OPCACHE__MEMORY_CONSUMPTION=256 \
    PHP_INI_OPCACHE__MAX_ACCELERATED_FILES=20000 \
    PHP_INI_OPCACHE__VALIDATE_TIMESTAMPS=0

# Stage 2: Dependencies
# Stage 2: Dependencies
FROM base AS dependencies

WORKDIR /app

# Copie des fichiers de dépendances
COPY app/composer.json app/composer.lock ./
COPY app/package.json ./

# Installation des dépendances Composer et NPM, AVEC "require-dev"
RUN COMPOSER_NO_DEV= composer install --no-scripts --prefer-dist
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
    && chmod -R 777 /app/var \
    && chmod -R 755 /app/public

# Production optimizations
COPY .env.example /app/.env
RUN rm -rf /app/node_modules \
    && composer dump-autoload --optimize

# Pas de script externe - commandes directes

# Port Railway
EXPOSE 8080

# Démarrage avec fixtures automatiques
ENTRYPOINT ["/start.sh"]