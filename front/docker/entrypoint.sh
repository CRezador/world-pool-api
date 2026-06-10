#!/bin/sh
set -e

# API_URL precisa estar definida com esquema (http:// ou https://), senao o
# proxy_pass gerado fica invalido e o nginx aborta com "invalid URL prefix".
if [ -z "${API_URL}" ]; then
    echo "ERRO: variavel de ambiente API_URL nao definida." >&2
    echo "Defina API_URL apontando para o servico da API (ex: http://api na rede do compose)." >&2
    exit 1
fi

case "${API_URL}" in
    http://*|https://*) ;;
    *)
        echo "ERRO: API_URL deve comecar com http:// ou https:// (valor atual: '${API_URL}')." >&2
        exit 1
        ;;
esac

# Porta interna do container; atras do Caddy usamos 80 como padrao.
export PORT="${PORT:-80}"

envsubst '${API_URL} ${PORT}' < /etc/nginx/conf.d/prod.conf.template > /etc/nginx/conf.d/default.conf

exec nginx -g "daemon off;"
