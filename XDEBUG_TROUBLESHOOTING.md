# Troubleshooting Xdebug - Porta em Uso

## Erro: "Listening to Xdebug has failed. No ports available"

Este erro ocorre quando a porta 9003 já está em uso por outro processo.

## Soluções

### Solução 1: Fechar outras instâncias do debugger

1. Feche todas as outras janelas do VS Code/Cursor que possam estar com o debugger ativo
2. Verifique se há outras instâncias rodando:
   ```bash
   ps aux | grep -i "code\|cursor" | grep -v grep
   ```
3. Feche todas as instâncias desnecessárias
4. Tente iniciar o debug novamente

### Solução 2: Usar porta alternativa (9004)

Se a porta 9003 continuar ocupada, você pode usar a porta 9004:

1. **Atualizar docker-compose-dev.yml:**
   ```yaml
   ports:
     - "9004:9004"  # Em vez de 9003:9003
   ```

2. **Atualizar Dockerfile:**
   ```dockerfile
   && echo "xdebug.client_port=9004" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
   ```

3. **Atualizar variável de ambiente no docker-compose-dev.yml:**
   ```yaml
   - XDEBUG_CONFIG=client_host=host.docker.internal client_port=9004 idekey=VSCODE
   ```

4. **Usar a configuração "Listen for Xdebug (Port 9004)" no launch.json**

5. **Reconstruir o container:**
   ```bash
   docker-compose -f docker-compose-dev.yml down
   docker-compose -f docker-compose-dev.yml build --no-cache app_nix
   docker-compose -f docker-compose-dev.yml up -d
   ```

### Solução 3: Matar processo na porta 9003

**⚠️ CUIDADO: Isso pode encerrar processos importantes**

```bash
# Identificar processo usando a porta
sudo lsof -i :9003

# Ou
sudo fuser 9003/tcp

# Matar o processo (substitua PID pelo número do processo)
sudo kill -9 <PID>
```

### Solução 4: Verificar se há múltiplas extensões PHP Debug

1. Abra as extensões no VS Code/Cursor (`Ctrl+Shift+X`)
2. Procure por "PHP Debug" ou "Xdebug"
3. Desabilite ou desinstale extensões duplicadas
4. Mantenha apenas uma extensão de debug PHP ativa

### Solução 5: Reiniciar o VS Code/Cursor

Às vezes, simplesmente reiniciar o editor resolve o problema:

1. Feche completamente o VS Code/Cursor
2. Verifique se não há processos órfãos:
   ```bash
   ps aux | grep cursor
   ```
3. Se houver, mate-os:
   ```bash
   pkill cursor
   ```
4. Abra o VS Code/Cursor novamente
5. Tente iniciar o debug

## Verificar se o Xdebug está funcionando

Após resolver o problema da porta, verifique se o Xdebug está configurado corretamente:

```bash
# Verificar se o Xdebug está instalado
docker exec -it nix_app php -v | grep -i xdebug

# Verificar configuração do Xdebug
docker exec -it nix_app php -i | grep xdebug

# Verificar se a porta está sendo exposta
docker exec -it nix_app netstat -tuln | grep 9003
```

## Configuração recomendada

Para evitar conflitos, recomendo usar a porta padrão 9003 e garantir que apenas uma instância do debugger esteja ativa por vez.
