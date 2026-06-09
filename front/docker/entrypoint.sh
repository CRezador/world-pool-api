#!/bin/sh
set -e

# API_URL precisa estar definida com esquema (http:// ou https://), senao o
# proxy_pass gerado fica invalido e o nginx aborta com "invalid URL prefix".
if [ -z "${API_URL}" ]; then
    echo "ERRO: variavel de ambiente API_URL nao definida." >&2
    echo "Defina API_URL no servico (ex: https://api-xxx.up.railway.app ou http://api.railway.internal:8080)." >&2
    exit 1
fi

case "${API_URL}" in
    http://*|https://*) ;;
    *)
        echo "ERRO: API_URL deve comecar com http:// ou https:// (valor atual: '${API_URL}')." >&2
        exit 1
        ;;
esac

# Railway injeta a porta via $PORT; localmente usamos 80 como padrao.
export PORT="${PORT:-80}"

envsubst '${API_URL} ${PORT}' < /etc/nginx/conf.d/railway.conf.template > /etc/nginx/conf.d/default.conf

exec nginx -g "daemon off;"
