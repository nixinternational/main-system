# Etapa 1: Composer (Gerenciamento de dependências PHP)
FROM php:8.2-fpm AS composer
WORKDIR /build
RUN apt-get update && apt-get install -y lsb-release
RUN echo "deb http://deb.debian.org/debian/ $(lsb_release -cs) main" > /etc/apt/sources.list

# Atualizar e instalar dependências necessárias para PHP e Composer
RUN apt-get update && apt-get install -y \
    build-essential nginx zlib1g-dev postgresql-client curl gnupg procps vim git unzip libzip-dev gnupg libpq-dev gcc g++ make \
    libicu-dev  \
    && docker-php-ext-install zip pdo_pgsql \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl

RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash -
RUN apt-get install -y nodejs

# Instalar Yarn globalmente
RUN npm install -g yarn
# Instalar PCOV para o Laravel
RUN pecl install pcov && docker-php-ext-enable pcov

# Instalar o Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN mv composer.phar /usr/local/bin/composer

# Configurar o Composer
ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_HOME /composer
ENV PATH $PATH:/composer/vendor/bin
RUN composer config --global process-timeout 3600
RUN composer global require "laravel/installer"

# Copiar os arquivos da aplicação
COPY . .

# Instalar dependências do Laravel
RUN composer install
RUN npm install
RUN npm run build

# Etapa 2: Node.js (instalar dependências do frontend)
FROM node:18-alpine AS npm
WORKDIR /app
COPY --from=composer /build .

RUN apk add git
RUN yarn install
RUN yarn build

# Etapa 3: Iniciar o PHP-FPM e Nginx
FROM composer AS run

WORKDIR /var/www/html

COPY --from=npm /app .

# Expor as portas
EXPOSE 80
EXPOSE 80
EXPOSE 443

# Permissões para o start.sh
COPY start.sh /start.sh
RUN chmod +x /start.sh

# CMD para rodar o Nginx e PHP-FPM
CMD ["/bin/bash", "/start.sh"]
