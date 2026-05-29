# Diário de estudo — Cobranca Platform

## Objetivo deste arquivo

Este arquivo registra o que foi construído, o que está pronto para demonstração e o que ainda está em modo esqueleto. A ideia é servir como material de estudo e revisão rápida.

## Situação atual do sistema

- Laravel 13.12 com Filament 5.6.
- PostgreSQL local funcionando.
- Painel administrativo acessível em `/admin`.
- Login seedado:
  - `admin@cobranca.local` / `Cobranca!2026`
  - `financeiro@cobranca.local` / `Cobranca!2026`
- MFA por aplicativo autenticador habilitado no Filament.
- Comandos destrutivos bloqueados em `APP_ENV=production`.
- Script PostgreSQL em `database/protection/install_db_protection.sql` para bloquear TRUNCATE/DROP em tabelas operacionais.
- Usuarios agora usam soft delete para preservar historico.
- Testes automatizados validados: **24 passed / 89 assertions**.

## O que já foi implementado

### Núcleo da cobrança

- Clientes.
- Cobranças.
- Parcelas.
- Boletos.
- Tarefas.
- Interações.
- Auditoria.
- Relatórios.
- Permissões com Spatie.

### POP Financeiro

- Tabelas novas para:
  - notas fiscais;
  - controle DDA;
  - ocorrências SERASA;
  - checklist POP financeiro.
- Service diário da régua POP em modo skeleton.
- Command agendável para processar a régua.
- Resource Filament para checklist POP.
- Resource Filament para notas fiscais, controle DDA e ocorrências SERASA.
- Policies para RBAC das novas entidades.
- Seeds com permissões e settings básicos.

## O que ficou propositalmente sem automação

- Nenhum envio automático de e-mail.
- Nenhum envio automático de WhatsApp.
- Nenhuma integração real com SERASA/DDA.
- Apenas estrutura, validações e trilha para apresentação.

## Por que a troca de páginas pode parecer lenta

No ambiente local, a navegação pode ficar mais pesada por alguns motivos:

- `debug` está ligado.
- O Filament faz requests novos a cada troca de tela.
- O banco é consultado a cada request.
- **Sessão, cache e fila estavam em modo de banco**, o que aumenta a dependência do PostgreSQL.
- Auditoria, permissões e observers adicionam trabalho.
- Se o PostgreSQL parar ou oscilar, a interface sente imediatamente.

## O que foi ajustado agora

Para aliviar a navegação no modo offline/local, o ambiente foi ajustado para usar:

- `SESSION_DRIVER=file`
- `CACHE_STORE=file`
- `QUEUE_CONNECTION=sync`

Isso reduz bastante o número de idas ao banco durante troca de páginas e login.

## Quando vale rodar online

Se você quiser editar o sistema por deploy, o caminho mais seguro é:

1. manter um repositório Git com branches;
2. subir em uma VPS ou ambiente de teste;
3. usar `.env` separado por ambiente;
4. rodar `php artisan migrate --force` no deploy;
5. executar `php artisan optimize:clear` e `php artisan config:cache` após cada release;
6. reiniciar fila/Horizon quando houver jobs novos;
7. testar login, painel e telas críticas antes de promover para produção.

## Ponto importante aprendido

A queda de energia mostrou que o sistema depende fortemente do PostgreSQL local. Quando o banco cai, o Laravel continua abrindo, mas a página quebra ao tentar buscar sessão e dados.

## Estrutura útil para estudo

- `app/Support/Billing/PopFinanceiroService.php`
- `app/Console/Commands/ProcessarPopFinanceiroCommand.php`
- `database/seeders/DatabaseSeeder.php`
- `app/Filament/Resources/PopFinanceiroChecklists/*`
- `app/Filament/Resources/NotasFiscais/*`
- `app/Filament/Resources/BoletoDdaControles/*`
- `app/Filament/Resources/SerasaOcorrencias/*`
- `tests/Feature/PopFinanceiroSkeletonTest.php`

## Próximos passos possíveis

- Documentar cada tela do Filament.
- Criar um passo a passo da demo.
- Adicionar capturas de tela e fluxos de usuário.
- Separar um glossário com os conceitos de cobrança, POP e compliance.
