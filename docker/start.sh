#!/bin/sh

echo "Starting Politricks application..."

# Wait a bit for database to be ready
sleep 5

# Run migrations in background to avoid blocking startup
(
  echo "Running database setup..."
  php bin/console doctrine:migrations:migrate --no-interaction 2>/dev/null || echo "Migrations failed or already applied"
  
  # Load fixtures if tables are empty
  USER_COUNT=$(php bin/console doctrine:query:sql "SELECT COUNT(*) FROM users" 2>/dev/null | tail -1 || echo "0")
  if [ "$USER_COUNT" = "0" ]; then
    echo "Loading fixtures..."
    php bin/console doctrine:fixtures:load --no-interaction 2>/dev/null || echo "Fixtures failed"
  fi
) &

# Start supervisor
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf