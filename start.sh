#!/bin/bash

# Inicializar o Laravel, se necessário

# Iniciar o PHP-FPM
php-fpm

# Iniciar o Nginx
# service nginx start

# Manter o contêiner em execução
tail -f /dev/null
