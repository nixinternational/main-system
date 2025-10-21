FROM nginx:latest AS nginx
WORKDIR /var/www/html

# Copiar a configuração do Nginx
COPY ./nginx-dev.conf /etc/nginx/conf.d/default.conf
