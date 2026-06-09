#!/bin/sh
set -e

echo "==> Aguardando banco de dados..."
until php artisan db:show --json > /dev/null 2>&1; do
  echo "    banco indisponível, aguardando 2s..."
  sleep 2
done

echo "==> Cacheando configuração..."
php artisan config:cache
php artisan route:cache

echo "==> Rodando migrations..."
php artisan migrate --force

echo "==> Subindo serviços..."
exec supervisord -c /etc/supervisor/conf.d/supervisord.conf
