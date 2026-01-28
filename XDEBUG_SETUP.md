# Configuração do Xdebug

O Xdebug está configurado e pronto para uso no ambiente de desenvolvimento Docker.

## Configurações Aplicadas

- **Porta**: 9003 (padrão do Xdebug 3)
- **Modo**: debug
- **IDE Key**: PHPSTORM
- **Start with request**: yes (inicia automaticamente)

## Como usar

### 1. Reconstruir o container

Após as mudanças no Dockerfile, você precisa reconstruir o container:

```bash
docker compose -f docker-compose-dev.yml down
docker compose -f docker-compose-dev.yml build --no-cache app_nix
docker compose -f docker-compose-dev.yml up -d
```

### 2. Verificar se o Xdebug está instalado

Execute dentro do container:

```bash
docker exec -it nix_app php -v
```

Você deve ver algo como: `with Xdebug v3.x.x`

Ou verificar a extensão:

```bash
docker exec -it nix_app php -m | grep xdebug
```

### 3. Configurar sua IDE

#### Para VS Code / Cursor

1. Instale a extensão "PHP Debug" (se ainda não tiver)
2. Crie/edite `.vscode/launch.json`:

```json
{
    "version": "0.2.0",
    "configurations": [
        {
            "name": "Listen for Xdebug",
            "type": "php",
            "request": "launch",
            "port": 9003,
            "pathMappings": {
                "/var/www/html": "${workspaceFolder}"
            },
            "log": true
        }
    ]
}
```

#### Para PHPStorm

1. Vá em `File > Settings > PHP > Servers`
2. Adicione um novo servidor:
   - **Name**: nix-app
   - **Host**: localhost
   - **Port**: 80
   - **Debugger**: Xdebug
   - **Use path mappings**: ✅
   - Mapeie: `/var/www/html` → `caminho/do/seu/projeto`

3. Vá em `File > Settings > PHP > Debug`
   - **Xdebug port**: 9003
   - **Can accept external connections**: ✅

4. Ative o "Start Listening for PHP Debug Connections" (ícone de telefone na barra superior)

### 4. Colocar breakpoints e debugar

1. Coloque breakpoints no seu código PHP
2. Ative o "Listen for Xdebug" na sua IDE
3. Acesse a aplicação em `http://localhost`
4. O debugger deve parar nos breakpoints

## Troubleshooting

### Xdebug não conecta

1. Verifique se a porta 9003 está aberta:
```bash
docker exec -it nix_app netstat -tuln | grep 9003
```

2. Verifique os logs do Xdebug:
```bash
docker exec -it nix_app tail -f /tmp/xdebug.log
```

3. Verifique a configuração do Xdebug:
```bash
docker exec -it nix_app php -i | grep xdebug
```

### Mudar o host do Xdebug

Se `host.docker.internal` não funcionar no seu sistema, você pode descobrir o IP do host:

```bash
# No Linux
ip addr show docker0 | grep inet

# No Mac/Windows
# host.docker.internal deve funcionar automaticamente
```

E então ajustar no `docker-compose-dev.yml`:

```yaml
environment:
  - XDEBUG_CONFIG=client_host=172.17.0.1 client_port=9003 idekey=PHPSTORM
```

### Desabilitar Xdebug temporariamente

Para melhorar performance quando não estiver debugando, você pode desabilitar:

```yaml
environment:
  - XDEBUG_MODE=off
```

Ou comentar a linha no `docker-compose-dev.yml` e reiniciar o container.

## Notas

- O Xdebug está configurado para iniciar automaticamente com cada requisição (`start_with_request=yes`)
- Para melhor performance, você pode mudar para `start_with_request=trigger` e usar um cookie/bookmarklet para ativar o debug apenas quando necessário
- Os logs do Xdebug estão em `/tmp/xdebug.log` dentro do container
