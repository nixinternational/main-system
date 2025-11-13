#!/bin/bash

# Inicializar o Laravel, se necessário

# Criar link simbólico para storage se não existir
if [ ! -L /var/www/html/public/storage ]; then
    php artisan storage:link
fi

# Configurar limites de upload do PHP (se não existir)
if [ ! -f /usr/local/etc/php/conf.d/uploads.ini ]; then
    echo "upload_max_filesize = 20M" > /usr/local/etc/php/conf.d/uploads.ini
    echo "post_max_size = 20M" >> /usr/local/etc/php/conf.d/uploads.ini
    echo "max_execution_time = 300" >> /usr/local/etc/php/conf.d/uploads.ini
    echo "max_input_time = 300" >> /usr/local/etc/php/conf.d/uploads.ini
    echo "memory_limit = 256M" >> /usr/local/etc/php/conf.d/uploads.ini
fi

# Iniciar o PHP-FPM
php-fpm

# Iniciar o Nginx
# service nginx start

# Manter o contêiner em execução
tail -f /dev/null
