# Manual de Seguranca Operacional

## Regra Mae

Ninguem, incluindo automacoes e agentes de IA, pode apagar, resetar ou recriar o banco de producao por terminal, Artisan, SQL ou painel.

Tambem e proibido sugerir esse caminho como solucao. Schema quebrado se corrige com migration nova, patch SQL conservador e `php artisan migrate --force`.

## Comandos Proibidos em Producao

- `php artisan migrate:fresh`
- `php artisan migrate:refresh`
- `php artisan migrate:reset`
- `php artisan migrate:rollback`
- `php artisan db:wipe`
- `php artisan db:seed`
- `php artisan key:generate`

O Laravel bloqueia esses comandos quando `APP_ENV=production`. A protecao fica em `App\Providers\AppServiceProvider` e nao deve ser removida.

## SQL Proibido

- `TRUNCATE` em tabelas operacionais.
- `DROP TABLE` em tabelas operacionais.
- `DROP SCHEMA` no schema da aplicacao.
- `DELETE` ou `UPDATE` sem filtro.

O script oficial de protecao PostgreSQL fica em `database/protection/install_db_protection.sql`.

## Banco de Producao

A aplicacao deve conectar com usuario de privilegio minimo:

- nao superusuario;
- sem `CREATEDB`;
- sem `CREATEROLE`;
- sem permissao para desabilitar triggers/event triggers;
- com acesso apenas ao banco e schema necessarios.

Ferramentas visuais como DBeaver devem usar um usuario separado e somente leitura. O script de referencia fica em `database/protection/create_readonly_user.sql`.

## Migrations

Migrations sao o historico oficial da estrutura do banco. Toda mudanca estrutural deve virar migration ou patch SQL versionado.

Em producao, o comando permitido para evoluir schema e:

```bash
php artisan migrate --force
```

Nunca use `migrate:fresh` como solucao para erro de schema em ambiente com dados reais.

## Backups e Restore

Backup em nuvem e Object Lock dependem de infraestrutura externa e devem ser configurados fora do codigo da aplicacao.

O restore deve ser fluxo controlado, com janela curta, responsavel identificado e bypass de protecao apenas na sessao do `pg_restore`.

## Exclusoes no Painel

Usuarios usam exclusao logica (`deleted_at`) para preservar historico e permitir recuperacao.

Tabelas de auditoria nao devem ter exclusao administrativa comum. Auditoria existe para explicar incidentes depois que eles acontecem.
