#!/usr/bin/env bash
set -e

: "${PORT:=10000}"

sed -i "s/^Listen .*/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:[0-9][0-9]*/<VirtualHost *:${PORT}/" /etc/apache2/sites-available/000-default.conf

mkdir -p \
  storage/app/public \
  storage/framework/cache/data \
  storage/framework/sessions \
  storage/framework/testing \
  storage/framework/views \
  storage/logs \
  bootstrap/cache

chown -R www-data:www-data storage bootstrap/cache || true

exec "$@"
