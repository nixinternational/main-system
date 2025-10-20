FROM nginx:latest

# Apenas para desenvolvimento, não precisa copiar SSL
WORKDIR /etc/nginx/conf.d
