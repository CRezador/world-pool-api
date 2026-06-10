#!/bin/sh
set -e

# APP_KEY e obrigatoria: sem ela o Laravel nao criptografa cookies/sessao e
# qualquer rota que toca sessao (ex: /sanctum/csrf-cookie) responde 500.
# Falha cedo com mensagem clara em vez de subir e dar "Server Error" silencioso.
if [ -z "${APP_KEY}" ]; then
    echo "ERRO: APP_KEY nao definida no .env.prod." >&2
    echo "Gere uma chave e preencha a linha APP_KEY= do .env.prod:" >&2
    echo '    echo "base64:$(openssl rand -base64 32)"' >&2
    exit 1
fi

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
