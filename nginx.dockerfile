FROM nginx:latest AS nginx
WORKDIR /var/www/html

# Copiar a configuração do Nginx
COPY ./nginx.conf /etc/nginx/conf.d/default.conf
