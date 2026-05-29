# Acesso ao banco de dados

Este guia explica como consultar o banco com seguranca, principalmente usando DBeaver, sem perder as protecoes do Laravel e do PostgreSQL.

## DBeaver

O DBeaver e um painel visual para bancos de dados. Ele permite navegar por tabelas, ver registros, filtrar dados e rodar consultas SQL sem depender apenas do terminal.

Use DBeaver para consultar e diagnosticar. Evite editar dados diretamente por ele, principalmente fora do ambiente local.

## Banco local SQLite

Quando o `.env` usa:

```env
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

Conexao no DBeaver:

- Driver: `SQLite`
- Database file: `database/database.sqlite`

Esse modo e simples para desenvolvimento local. Ele nao representa perfeitamente o PostgreSQL de producao, mas e util para estudar as tabelas.

## PostgreSQL Docker

Quando usar a stack do `docker-compose.yml`, os dados padrao sao:

- Host: `127.0.0.1`
- Port: `5433`
- Database: `cobranca_platform`
- User: `postgres`
- Password: `postgres`

Essas credenciais sao locais e didaticas. Nao use esse padrao em producao.

## Usuario read-only para DBeaver

Para olhar dados com menos risco, prefira um usuario somente leitura. O script oficial fica em:

```text
database/protection/create_readonly_user.sql
```

Exemplo:

```powershell
psql "$env:DATABASE_URL" -f database/protection/create_readonly_user.sql
```

Depois conecte no DBeaver com:

- User: `cobranca_readonly`
- Password: a senha escolhida no script

Esse usuario consegue consultar tabelas e sequencias, mas nao deve conseguir alterar, apagar ou recriar dados.

## Migrations

Migration e o historico versionado da estrutura do banco. Cada arquivo em `database/migrations` descreve uma mudanca: criar tabela, adicionar coluna, criar indice, ou preparar campos novos.

Com isso, outra maquina consegue reconstruir a mesma estrutura rodando:

```powershell
php artisan migrate
```

Em producao, use:

```powershell
php artisan migrate --force
```

Nunca corrija schema quebrado apagando tudo. O caminho correto e criar outra migration ou um patch SQL conservador.

## Comandos perigosos

`migrate:fresh` apaga todas as tabelas e recria o banco do zero. Isso pode ser util em ambiente local descartavel, mas e catastrofico em producao.

Em `APP_ENV=production`, o projeto bloqueia comandos destrutivos no `AppServiceProvider`.

Comandos proibidos em producao:

- `php artisan migrate:fresh`
- `php artisan migrate:refresh`
- `php artisan migrate:reset`
- `php artisan migrate:rollback`
- `php artisan db:wipe`
- `php artisan db:seed`
- `php artisan key:generate`

## IA no terminal

Assistentes dentro de editores conseguem executar comandos no terminal integrado. Isso e util, mas comandos de banco precisam de cuidado.

Antes de rodar comandos em banco real, confirme:

- qual ambiente esta ativo;
- qual arquivo `.env` esta sendo usado;
- qual banco aparece em `DB_HOST`, `DB_PORT` e `DB_DATABASE`;
- se o comando altera estrutura ou dados;
- se existe backup recente.

A IA pode sugerir e executar comandos, mas a decisao de rodar algo destrutivo nunca deve ser automatica.
