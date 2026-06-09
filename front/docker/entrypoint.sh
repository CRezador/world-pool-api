#!/bin/sh
set -e

envsubst '${API_URL}' < /etc/nginx/conf.d/railway.conf.template > /etc/nginx/conf.d/default.conf

exec nginx -g "daemon off;"
