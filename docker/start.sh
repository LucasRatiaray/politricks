#!/bin/bash

# Attendre que la base de données soit prête
until php bin/console doctrine:query:sql "SELECT 1" 2>/dev/null; do
    echo "Waiting for database..."
    sleep 2
done

# Lancer les migrations
echo "Running database migrations..."
php bin/console doctrine:migrations:migrate --no-interaction

# Charger les fixtures seulement si pas déjà présentes
echo "Checking if fixtures are needed..."
USER_COUNT=$(php bin/console doctrine:query:sql "SELECT COUNT(*) FROM \"user\"" 2>/dev/null | tail -1 || echo "0")
if [ "$USER_COUNT" = "0" ]; then
    echo "Loading fixtures..."
    php bin/console doctrine:fixtures:load --no-interaction
else
    echo "Fixtures already loaded, skipping..."
fi

# Démarrer Supervisor
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf