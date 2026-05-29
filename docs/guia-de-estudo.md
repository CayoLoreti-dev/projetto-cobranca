# Guia de estudo — Cobranca Platform

## Visão geral

Este projeto é um sistema de cobrança em Laravel com painel administrativo em Filament. A base foi pensada para ser modular, auditável e pronta para crescer sem virar uma mistura de lógica dentro das telas.

## Stack principal

- Laravel 13
- PHP 8.4
- Filament 5
- PostgreSQL
- Spatie Permission
- Sanctum
- Queue database / Horizon preparado
- Vite para assets

## Como pensar a arquitetura

### Models

Os models representam as entidades centrais do negócio: cliente, cobrança, parcela, boleto, tarefa, interação e entidades do POP Financeiro.

### Enums

Os enums evitam strings soltas para status e tipos. Isso ajuda a reduzir erro de digitação e deixa as regras mais claras.

### Actions

As actions concentram casos de uso. Exemplo: criar cobrança, registrar nota fiscal, executar regras do POP.

### Services

Os services concentram regras de processo e integração entre modelos. Exemplo: a régua diária do POP Financeiro.

### Policies

As policies fazem a ponte entre autenticação e autorização.

### Filament

O Filament entrega as telas administrativas. Ele é útil porque transforma o backoffice em páginas rápidas de operação.

## Por que o sistema pode parecer lento em desenvolvimento

- Cada troca de página costuma gerar novas consultas no banco.
- O ambiente está em modo local com debug.
- Banco, sessão e cache rodam localmente.
- Auditoria e observers adicionam trabalho em cada request.
- Se o PostgreSQL travar, a página responde com erro imediatamente.

## Fluxo de estudo recomendado

1. Abrir o `README.md`.
2. Ler `docs/diario-de-estudo.md`.
3. Explorar `app/Models` e `app/Enums`.
4. Ler `app/Support/Billing/PopFinanceiroService.php`.
5. Ler `database/seeders/DatabaseSeeder.php`.
6. Abrir os testes em `tests/Feature`.
7. Conferir as páginas do Filament.

## Módulo POP Financeiro

O POP foi implementado como esqueleto operacional:

- checklist diário;
- skeleton de emissão/registro de nota fiscal;
- skeleton de controle DDA;
- skeleton de ocorrência SERASA;
- seeds para permissões e configurações.

### Telas do Filament já disponíveis

- `PopFinanceiroChecklists` — visão do checklist operacional;
- `NotasFiscais` — acompanhamento de NF por cobrança/boletos;
- `BoletoDdaControles` — controle de presença em DDA;
- `SerasaOcorrencias` — fila de ocorrências e etapas Serasa.

## O que não está automatizado de propósito

- E-mail automático.
- WhatsApp automático.
- Integrações externas reais.

## Como usar este material

Use este guia para entender a estrutura geral, e o diário para acompanhar o histórico das alterações mais recentes. Juntos eles funcionam como uma trilha de estudo do sistema.
