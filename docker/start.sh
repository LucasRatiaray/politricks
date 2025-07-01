#!/bin/sh

echo "Starting Politricks application..."

# Ensure proper permissions
chown -R www-data:www-data /app/var
chmod -R 777 /app/var

# Wait for database and setup
(
  sleep 10
  echo "Setting up database..."
  
  # Try migrations with detailed output
  php bin/console doctrine:migrations:migrate --no-interaction || echo "Migration error"
  
  # Check if users table exists and load fixtures
  if php bin/console doctrine:query:sql "SELECT 1 FROM users LIMIT 1" 2>/dev/null; then
    echo "Database tables exist"
  else
    echo "Loading fixtures..."
    php bin/console doctrine:fixtures:load --no-interaction || echo "Fixtures error"
  fi
) &

# Start supervisor
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf