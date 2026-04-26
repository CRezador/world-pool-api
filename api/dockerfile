FROM php:8.4-fpm-alpine

# Dependências do sistema + extensões PHP necessárias para Laravel/MySQL
RUN apk add --no-cache \
    bash git curl unzip \
    icu-dev oniguruma-dev libzip-dev \
    mysql-client \
    nodejs npm \
  && docker-php-ext-install \
    pdo pdo_mysql mbstring intl zip opcache \
  && rm -rf /var/cache/apk/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copia apenas arquivos do composer primeiro (melhor cache)
COPY composer.json composer.lock* ./

# Instala deps PHP (sem scripts aqui pra evitar rodar artisan sem env ainda)
RUN composer install --no-interaction --no-progress --prefer-dist --no-scripts

# Copia o restante do projeto
COPY . .
RUN composer dump-autoload -o

# Permissões (storage/cache)
RUN mkdir -p storage bootstrap/cache \
  && chown -R www-data:www-data storage bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]