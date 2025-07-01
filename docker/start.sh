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
  
  # Check if users table has data and load fixtures if needed
  echo "Checking user count..."
  USER_COUNT=$(su www-data -s /bin/sh -c "php bin/console doctrine:query:sql 'SELECT COUNT(*) FROM users'" 2>/dev/null | tail -1 | tr -d ' ' || echo "0")
  echo "Found $USER_COUNT users in database"
  
  # Force loading fixtures for now to debug
  echo "Force loading fixtures..."
  su www-data -s /bin/sh -c "php bin/console doctrine:fixtures:load --no-interaction" && echo "Fixtures loaded successfully" || echo "Fixtures error"
  
  # Warm up cache as www-data
  echo "Warming up cache..."
  su www-data -s /bin/sh -c "php bin/console cache:warmup --no-interaction" || echo "Cache warmup error"
) &

# Start supervisor
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf