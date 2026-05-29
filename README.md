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
c:\Users\T-GAMER\Downloads\cayo\projetto-cobranca-main\cobranca-platform
```

Servidor local ja validado:

```text
http://127.0.0.1:8000/admin
```

Credenciais seedadas:

```text
admin@cobranca.local / cobranca123
financeiro@cobranca.local / cobranca123
```

## Rodando

```powershell
$env:Path = [Environment]::GetEnvironmentVariable('Path','User') + ';' + [Environment]::GetEnvironmentVariable('Path','Machine')
cd c:\Users\T-GAMER\Downloads\cayo\projetto-cobranca-main\cobranca-platform
php artisan serve --host=127.0.0.1 --port=8000
```

Para uma instalacao limpa:

```powershell
composer install --ignore-platform-req=ext-pcntl --ignore-platform-req=ext-posix
npm install
copy .env.example .env
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

## Validacao

```powershell
php artisan test
npm run build
php artisan route:list --except-vendor
```

Ultima validacao executada:

```text
12 testes verdes, 37 assertions
49 rotas de aplicacao
build Vite concluido
```

## Material de estudo

Para manter o projeto como referencia didatica, consulte:

- `docs/guia-de-estudo.md` — resumo tecnico do que existe hoje, o que foi implementado e por que certas escolhas foram feitas.
- `docs/diario-de-estudo.md` — trilha das mudancas recentes, incluindo POP Financeiro, permissões, seeds e pontos de atenção.

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
