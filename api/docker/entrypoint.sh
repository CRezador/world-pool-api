#!/bin/sh
set -e

# Porta interna do container; atras do Caddy usamos 80 como padrao.
# So substituimos ${PORT} para preservar as variaveis do nginx ($uri, $fastcgi_*, ...).
export PORT="${PORT:-80}"
echo "==> Gerando config do nginx na porta ${PORT}..."
envsubst '${PORT}' < /etc/nginx/http.d/default.conf.template > /etc/nginx/http.d/default.conf

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
