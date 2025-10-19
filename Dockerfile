# Etapa 1: PHP + Composer
FROM php:8.2-fpm AS composer
WORKDIR /build

# Instalar dependências do sistema
RUN apt-get update && apt-get install -y \
    lsb-release build-essential nginx zlib1g-dev postgresql-client curl gnupg procps vim git unzip libzip-dev libpq-dev gcc g++ make libicu-dev \
    && docker-php-ext-install zip pdo_pgsql intl pgsql

# Node.js e Yarn
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g yarn

# PCOV para cobertura do Laravel
RUN pecl install pcov && docker-php-ext-enable pcov

# Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php \
    && php -r "unlink('composer-setup.php');" \
    && mv composer.phar /usr/local/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_HOME /composer
ENV PATH $PATH:/composer/vendor/bin

# Config Composer
RUN composer config --global process-timeout 3600 \
    && composer global require "laravel/installer"

# Copiar a aplicação
COPY . .

# Instalar dependências do Laravel + frontend
RUN composer install --no-interaction --no-progress --ignore-platform-reqs
RUN npm install
RUN npm run build

# Etapa 2: Node (frontend)
FROM node:18-alpine AS npm
WORKDIR /app
COPY --from=composer /build .

# instalar git + dependências necessárias para yarn
RUN apk add --no-cache git

RUN yarn install
RUN yarn build


# Etapa 3: PHP-FPM + Nginx (runtime)
FROM php:8.2-fpm AS run
WORKDIR /var/www/html

# Copiar arquivos do build final
COPY --from=npm /app .

# Expor portas
EXPOSE 80
EXPOSE 443
EXPOSE 9000

# Start script
COPY start.sh /start.sh
RUN chmod +x /start.sh

# CMD para rodar Nginx + PHP-FPM
CMD ["/bin/bash", "/start.sh"]
