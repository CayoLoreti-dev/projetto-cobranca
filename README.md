# Cobranca Platform

Sistema greenfield em Laravel para gestao de cobrancas, com painel administrativo, APIs JSON, RBAC, auditoria, eventos de dominio, filas e base preparada para PostgreSQL/Redis/object storage.

## Stack

- Laravel 13, PHP 8.4 local
- PostgreSQL transacional
- Filament para painel admin
- Spatie Laravel Permission para RBAC
- Sanctum para API tokens
- Laravel Queues e Horizon prontos para Redis
- Scout instalado para busca futura com engine de banco no inicio
- Vite/Tailwind para assets

## Estrutura

- `app/Models`: entidades principais do dominio
- `app/Enums`: status e tipos fechados do dominio
- `app/Actions`: casos de uso e regras de negocio
- `app/Support/Audit`: trilha de auditoria e eventos imutaveis
- `app/Support/Billing`: regras de vencimento, atraso e dados de boleto
- `app/Support/Reports`: consultas de relatorios fora dos controllers
- `app/Jobs`: tarefas assincronas
- `app/Observers`: auditoria automatica de mudancas relevantes
- `app/Policies`: autorizacao por permissao
- `app/Filament/Resources`: painel administrativo
- `app/Http/Controllers/Api/V1`: API versionada
- `tests`: testes de regras, permissoes, relatorios e fluxos criticos

## Modulos Entregues

- Usuarios, papeis e permissoes: `ADMIN`, `FINANCEIRO`, `OPERADOR`, `LEITURA`
- Clientes
- Cobrancas
- Parcelas, baixa de pagamento e recalculo da cobranca
- Boletos com metadados e job de geracao demonstrativo
- Tarefas/minhas demandas
- Interacoes por canal
- Configuracoes do sistema
- Auditoria dedicada e eventos por entidade
- Relatorios API: resumo, inadimplencia, previsao de recebimento, produtividade e evolucao temporal

## Ambiente Local Atual

O projeto novo esta isolado em:

```powershell
c:\Users\OPERADOR\Downloads\projetto-cobranca-cobranca-platform\projetto-cobranca-cobranca-platform
```

Servidor local ja validado:

```text
http://127.0.0.1:8000/admin
```

Credenciais seedadas:

```text
admin@cobranca.local / Cobranca!2026
financeiro@cobranca.local / Cobranca!2026
```

No primeiro acesso ao painel, o Filament pede configuracao de MFA por aplicativo autenticador.

## Comeco rapido

Esse eh o caminho mais curto para colocar o projeto de pe em um clone novo:

```powershell
copy .env.example .env
New-Item -ItemType File -Force database\database.sqlite | Out-Null
php artisan key:generate
php artisan migrate --seed
npm install
npm run build
php artisan serve --host=127.0.0.1 --port=8000
```

Se preferir usar a stack completa com PostgreSQL, Redis, MinIO e Mailpit, copie `.env.docker.example` para `.env` e rode o `docker compose up -d`.

## Rodando

```powershell
$env:Path = [Environment]::GetEnvironmentVariable('Path','User') + ';' + [Environment]::GetEnvironmentVariable('Path','Machine')
cd c:\Users\OPERADOR\Downloads\projetto-cobranca-cobranca-platform\projetto-cobranca-cobranca-platform
php artisan serve --host=127.0.0.1 --port=8000
```

Para uma instalacao limpa:

```powershell
composer install --ignore-platform-req=ext-pcntl --ignore-platform-req=ext-posix
npm install
copy .env.example .env
New-Item -ItemType File -Force database\database.sqlite | Out-Null
php artisan key:generate
php artisan migrate --seed
npm run build
```

No Windows, Horizon pode exigir `--ignore-platform-req=ext-pcntl --ignore-platform-req=ext-posix` no Composer. Em Linux/produção, use as extensoes normalmente.

## Infra Completa Recomendada

O arquivo `docker-compose.yml` sobe PostgreSQL, Redis, MinIO e Mailpit. Para usar essa stack, copie `.env.docker.example` para `.env`, ajuste segredos e rode:

```powershell
docker compose up -d
php artisan migrate --seed
php artisan horizon
```

No ambiente local atual, `SESSION_DRIVER`, `CACHE_STORE` e `QUEUE_CONNECTION` estao em `file`/`sync` para reduzir carga no PostgreSQL durante desenvolvimento. A stack Redis/Horizon ja esta instalada e fica ativa ao usar o `.env.docker.example`.

## Colocando online

Para publicar o projeto em produção, o caminho mais simples e confiavel eh usar uma VPS com Ubuntu, Nginx e PHP 8.4.

Use `.env.production.example` como referencia de variaveis seguras. Em produção, `APP_ENV=production` e regra inviolavel porque habilita as travas contra comandos destrutivos.

### Configuracao recomendada

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://seu-dominio.com`
- `SESSION_SECURE_COOKIE=true`
- `SECURITY_PASSWORD_UNCOMPROMISED=true`
- `DB_CONNECTION=pgsql`
- `SESSION_DRIVER=redis`
- `CACHE_STORE=redis`
- `QUEUE_CONNECTION=redis`
- `FILESYSTEM_DISK=s3` ou equivalente em objeto/armazenamento externo

### Passos resumidos

```powershell
composer install --no-dev --optimize-autoloader
npm install
npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link
```

Depois disso, coloque o Nginx apontando para a pasta `public/`, rode o PHP-FPM e configure um worker para filas/Horizon com Supervisor.

### Para ficar mais rapido em produção

- use Postgres gerenciado ou em servidor dedicado;
- use Redis para cache, sessão e filas;
- mantenha os assets gerados com `npm run build`;
- habilite HTTPS;
- desative `APP_DEBUG`;
- configure backups do banco e do storage.
- instale as travas PostgreSQL com `database/protection/install_db_protection.sql`.

### Ajustes de banco para produção

Para uma base PostgreSQL em produção, aplique os índices compostos e de checksum que já estão alinhados com as migrations do projeto. Eles ajudam principalmente nas listas ordenadas e nas trilhas de auditoria.

Arquivo pronto para execução manual:

- `database/postgres_indexes.sql`

Exemplo de execução:

```powershell
psql "$env:DATABASE_URL" -f database/postgres_indexes.sql
```

### Paginação mais robusta

As listagens de API mais pesadas agora usam paginação por cursor (`cursorPaginate`) em vez de `paginate`. Isso reduz custo quando a tabela cresce bastante, porque o banco continua andando a partir do último registro visto, sem depender de `OFFSET` grande.

Se quiser levar isso ainda mais longe, o próximo passo é revisar as telas do Filament que mais concentram volume e buscar uma estratégia equivalente de navegação/ordenação para listas grandes.

## Validacao

```powershell
php artisan test
npm run build
php artisan route:list --except-vendor
```

Ultima validacao executada no ambiente atual:

```text
24 testes verdes, 89 assertions
build Vite concluido
```

## Material de estudo

Para manter o projeto como referencia didatica, consulte:

- `docs/guia-de-estudo.md` — resumo tecnico do que existe hoje, o que foi implementado e por que certas escolhas foram feitas.
- `docs/diario-de-estudo.md` — trilha das mudancas recentes, incluindo POP Financeiro, permissões, seeds e pontos de atenção.
- `docs/acesso-ao-banco.md` — guia pratico para DBeaver, migrations e comandos seguros de banco.

## Performance local

Se a troca de páginas parecer lenta no navegador, os motivos mais comuns neste ambiente sao:

- modo `local` com `debug` habilitado;
- cada navegação do Filament faz novas consultas ao banco;
- sessão, cache e filas estão em `file`/`sync` no modo local para aliviar o PostgreSQL;
- banco e app rodam localmente, então qualquer travada do PostgreSQL afeta a UI imediatamente;
- observers, auditoria e permissões adicionam trabalho a cada requisição.

Isso não é necessariamente um bug. Pode ser só o custo normal de um sistema com muita consulta e auditoria em ambiente de desenvolvimento.

## POP Financeiro demo

As telas de demonstração já incluem:

- checklist POP financeiro;
- notas fiscais;
- controle DDA;
- ocorrências SERASA.

Não há automação real de envio por e-mail, WhatsApp ou integrações externas; a ideia é apenas apresentar o esqueleto funcional.
