# Etapa 1: PHP + Composer (build)
FROM php:8.2-fpm AS composer
WORKDIR /build

# Instalar dependências do sistema para PHP, PostgreSQL, Node e compilação
RUN apt-get update && apt-get install -y \
    lsb-release build-essential zlib1g-dev postgresql-client curl gnupg procps git unzip libzip-dev libpq-dev gcc g++ make libicu-dev nano vim \
    && docker-php-ext-install zip pdo_pgsql intl pgsql \
    && rm -rf /var/lib/apt/lists/*

# Node.js e Yarn
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
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
FROM node:20-alpine AS npm
WORKDIR /app
COPY --from=composer /build .

# instalar git + dependências necessárias para yarn
RUN apk add --no-cache git bash nano vim

RUN yarn install
RUN yarn build

# Etapa 3: PHP-FPM + Nginx (runtime)
FROM php:8.2-fpm AS run
WORKDIR /var/www/html

# Instalar dependências mínimas para runtime
RUN apt-get update && apt-get install -y \
    postgresql-client libpq-dev nano vim \
    && docker-php-ext-install pdo_pgsql \
    && rm -rf /var/lib/apt/lists/*

# Instalar Xdebug para desenvolvimento
RUN pecl install xdebug && docker-php-ext-enable xdebug

# Configurar limites de upload do PHP
RUN echo "upload_max_filesize = 20M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size = 20M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "max_execution_time = 300" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "max_input_time = 300" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "memory_limit = 256M" >> /usr/local/etc/php/conf.d/uploads.ini

# Configurar Xdebug (valores padrão, podem ser sobrescritos por variáveis de ambiente)
RUN echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_port=9003" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.idekey=VSCODE" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.log=/tmp/xdebug.log" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.log_level=0" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.discover_client_host=true" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Copiar arquivos do build final
COPY --from=npm /app .

# Expor portas
EXPOSE 80
EXPOSE 443
EXPOSE 9000
EXPOSE 9003

# Start script
COPY start.sh /start.sh
RUN chmod +x /start.sh

# CMD para rodar Nginx + PHP-FPM
CMD ["/bin/bash", "/start.sh"]
