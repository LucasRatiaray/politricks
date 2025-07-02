#!/bin/sh

echo "Starting Politricks application..."

# Ensure proper permissions
chown -R www-data:www-data /app/var /app/public
chmod -R 755 /app/var /app/public

# Setup database synchronously to see errors
echo "Setting up database..."

# Create database if it doesn't exist
su www-data -s /bin/sh -c "php bin/console doctrine:database:create --if-not-exists --no-interaction" || echo "Database creation error"

# Run migrations as www-data to avoid permission issues  
su www-data -s /bin/sh -c "php bin/console doctrine:migrations:migrate --no-interaction" || echo "Migration error"

# Load fixtures only if no users exist
echo "Checking if fixtures are needed..."
USER_COUNT=$(su www-data -s /bin/sh -c "php bin/console doctrine:query:sql 'SELECT COUNT(*) FROM user'" 2>/dev/null | tail -1 | awk '{print $1}')
if [ "$USER_COUNT" = "0" ] || [ -z "$USER_COUNT" ]; then
  echo "No users found, loading fixtures..."
  su www-data -s /bin/sh -c "php bin/console doctrine:fixtures:load --no-interaction --purge-with-truncate" && echo "Fixtures loaded successfully" || echo "Fixtures error"
else
  echo "Users already exist ($USER_COUNT users), skipping fixtures"
fi

# Warm up cache as www-data
echo "Warming up cache..."
su www-data -s /bin/sh -c "php bin/console cache:warmup --no-interaction" || echo "Cache warmup error"

echo "Setup completed, starting services..."

# Start supervisor
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf