#!/bin/sh

# Start application directly
echo "Starting Politricks application..."

# Start supervisor directly
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf