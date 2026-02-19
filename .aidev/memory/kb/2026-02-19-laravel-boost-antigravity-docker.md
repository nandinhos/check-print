# Licao: Configurar Laravel Boost MCP no Antigravity / Gemini CLI com Docker

**Data**: 2026-02-19
**Stack**: Laravel 12, Docker, Gemini CLI (Antigravity), MCP
**Tags**: config, deployment, integration, mcp, docker

---

## Contexto

**Stack**: Laravel 12 + Docker (sem Laravel Sail nativo — container customizado)
**Ambiente**: Desenvolvimento local com Gemini CLI (Antigravity)
**Frequencia**: Uma vez por projeto Laravel novo
**Impacto**: Alto — sem isso o MCP laravel-boost nao funciona e nao ha acesso a ferramentas de DB, tinker, rotas, etc.

### Sintoma Observado
O MCP `laravel-boost` nao inicializava no Gemini CLI. O `mcp_config.json` apontava para container e caminhos errados, e o comando artisan estava incorreto.

### Comportamento Esperado
O servidor MCP `laravel-boost` deve inicializar corretamente ao iniciar o Gemini CLI, dando acesso a ferramentas como `database-query`, `tinker`, `list-routes`, etc.

---

## Causa Raiz

### Analise (5 Whys)
1. **Por que o MCP nao iniciava?** O `artisan boost:mcp` nao existia no container.
2. **Por que o comando nao existia?** O pacote `laravel/boost` estava no `composer.json` mas nao havia sido instalado via `composer install` (vendor desatualizado ou pacote adicionado manualmente ao json).
3. **Por que o mcp_config.json estava errado?** Foi configurado manualmente com dados incorretos: container nginx em vez do container da app, path errado do artisan, e nome do comando errado (era `mcp:start laravel-boost` — comando legado ou inventado).
4. **Por que o comando estava errado?** Nao havia documentacao interna sobre a configuracao correta para esse ambiente especifico.
5. **Causa raiz**: Ausencia de padrao documentado para configurar Laravel Boost no Antigravity com container Docker customizado.

### Causa Raiz Identificada
Combinacao de tres erros na configuracao do `mcp_config.json` + pacote nao corretamente instalado no vendor do container.

### Tipo de Problema
- [x] Configuracao incorreta
- [x] Integration (MCP + Docker)

---

## Solucao

### Passo 1: Instalar o pacote no container

```bash
# Ajustar permissao primeiro (se necessario)
docker exec -u root <container_app> chmod 664 /var/www/composer.json /var/www/composer.lock
docker exec -u root <container_app> chown www-data:www-data /var/www/composer.json /var/www/composer.lock

# Instalar o pacote
docker exec <container_app> composer require laravel/boost --dev
```

### Passo 2: Verificar o comando disponivel

```bash
docker exec <container_app> php /var/www/artisan boost:mcp --help
```

Se aparecer o help, o pacote esta instalado e o ServiceProvider foi registrado corretamente.

### Passo 3: Configurar o mcp_config.json CORRETAMENTE

Arquivo: `~/.gemini/antigravity/mcp_config.json`

```json
{
    "mcpServers": {
        "laravel-boost": {
            "command": "docker",
            "args": [
                "exec",
                "-i",
                "<nome_do_container_app>",
                "php",
                "/var/www/artisan",
                "boost:mcp"
            ]
        }
    }
}
```

**Pontos criticos:**
| Campo | Valor correto | Erro comum |
|-------|--------------|------------|
| Container | Container da **app PHP** (ex: `check_print_app`) | Usar container do nginx |
| Path artisan | `/var/www/artisan` | `/var/www/html/artisan` |
| Comando | `boost:mcp` | `mcp:start laravel-boost` (inexiste) |
| Flag docker | `-i` (stdin) | Sem flag ou `-it` (quebra MCP) |

### Por Que Funciona
O MCP usa stdio (stdin/stdout) para comunicacao. O `docker exec -i` mantem o stdin aberto sem alocar TTY, que e exatamente o que o protocolo MCP precisa. O `boost:mcp` e o comando correto registrado pelo `BoostServiceProvider` no Laravel 12.

---

## Sobre o `boost:install`

O comando `boost:install` e **interativo** e nao funciona sem TTY em containers. Ele serve para configurar agentes especificos (Cursor, Claude Code, etc.) — **nao e necessario para o Gemini CLI (Antigravity)**. O suficiente e ter o `boost:mcp` funcionando.

Se precisar rodar `boost:install` no container para configurar guidelines/skills, deve-se usar flags explicitas para pular os prompts:

```bash
# Isso ainda pode falhar por prompt de selecao de agente sem TTY
docker exec check_print_app php artisan boost:install --guidelines --skills --mcp --no-interaction

# Alternativa: rodar interativamente com -it (mas nao funciona via MCP)
docker exec -it check_print_app php artisan boost:install
```

---

## Checklist de Verificacao

- [ ] Container correto identificado: `docker ps` para listar, usar o container PHP/FPM, nao nginx
- [ ] Path do artisan verificado: `docker exec <app> find / -name "artisan" -maxdepth 5`
- [ ] Pacote instalado: `docker exec <app> php artisan list | grep boost`
- [ ] Comando funcionando: `docker exec <app> php artisan boost:mcp --help`
- [ ] mcp_config.json com `-i` (nao `-t`, nao `-it`)
- [ ] Reiniciar o Gemini CLI apos alterar mcp_config.json

---

## Prevencao

- Ao criar novo projeto Laravel com Docker, rodar `composer require laravel/boost --dev` no container logo apos o setup inicial
- Sempre verificar o nome do container com `docker ps` antes de configurar o mcp_config.json
- Nunca usar o container nginx como target — usar sempre o container que roda o PHP

---

## Referencias
- Documentacao oficial: https://laravel.com/docs/12.x/boost#manually-registering-the-mcp-server
- Arquivo configurado: `~/.gemini/antigravity/mcp_config.json`
