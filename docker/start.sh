#!/bin/sh

echo "Starting Politricks application..."

# Ensure proper permissions
chown -R www-data:www-data /app/var /app/public
chmod -R 755 /app/var /app/public

# Wait for database and setup as www-data user
(
  sleep 10
  echo "Setting up database..."
  
  # Run as www-data to avoid permission issues
  su www-data -s /bin/sh -c "php bin/console doctrine:migrations:migrate --no-interaction" || echo "Migration error"
  
  # Check if users table exists  
  if su www-data -s /bin/sh -c "php bin/console doctrine:query:sql 'SELECT 1 FROM users LIMIT 1'" 2>/dev/null; then
    echo "Database tables exist"
  else
    echo "Database setup complete - fixtures not available in prod"
  fi
  
  # Warm up cache as www-data
  echo "Warming up cache..."
  su www-data -s /bin/sh -c "php bin/console cache:warmup --no-interaction" || echo "Cache warmup error"
) &

# Start supervisor
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf