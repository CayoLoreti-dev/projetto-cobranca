# Guia didático

## Ideia central

Este sistema é um monólito modular: front-end, back-end e acesso ao banco ficam no mesmo projeto Next.js, mas separados por pastas e responsabilidades. Isso é mais simples para uma pessoa manter do que microserviços.

## Por que TypeScript

TypeScript adiciona tipos ao JavaScript. Na prática, ele avisa antes do sistema rodar quando você passa um campo errado, esquece uma propriedade ou mistura formatos. Para quem vem de Java, pense nele como uma forma de trazer parte da segurança de tipos do Java para o mundo JavaScript.

## Por que Prisma

Prisma é a camada de acesso ao banco. Em Java/Spring, você poderia usar JPA/Hibernate ou Spring Data. Aqui, o `schema.prisma` descreve tabelas, campos, enums e relacionamentos; depois o Prisma gera um client tipado para consultar e gravar dados.

## Por que PostgreSQL

PostgreSQL é um banco relacional robusto. Ele combina bem com cobrança porque os dados têm relações fortes: cliente tem cobranças, cobrança tem parcelas, parcela pode ter boleto, e tudo precisa ser auditável.

## Por que Zod

Zod valida dados em tempo de execução. TypeScript ajuda durante o desenvolvimento, mas um JSON vindo de API pode chegar errado. Zod confere esse JSON antes de salvar. Em Java, isso lembra DTOs com anotações como `@NotBlank`, `@Email` e `@Positive`.

## Fluxo formulário -> banco

1. O usuário preenche a tela.
2. O componente de formulário usa React Hook Form.
3. O Zod valida o objeto.
4. O front envia `fetch("/api/...")`.
5. A rota de API valida de novo.
6. A rota chama Prisma para gravar.
7. A rota cria auditoria/interação quando necessário.
8. A página mostra os dados atualizados.

## Onde ficam as regras

As regras que não pertencem à tela ficam em `src/lib/server/rules.ts`. Exemplo: gerar tarefas para 5, 10 e 30 dias. Essa separação evita que regra de cobrança fique espalhada em botões e componentes visuais.

## Onde ficam os controllers

No App Router, arquivos `route.ts` dentro de `src/app/api` funcionam como controllers. Um `POST /api/clientes` cria cliente; um `GET /api/clientes` lista clientes.

## Onde fica a UI

As páginas ficam em `src/app/(app)`. Componentes reutilizáveis ficam em `src/components`. Formulários ficam em `src/components/forms` para separar telas de lógica de entrada.

## Usuários e demandas

O modelo `Usuario` representa cada pessoa do setor. Cada `Tarefa` pode apontar para `responsavelId`, que é o vínculo com o usuário responsável. Em Java/Spring, isso lembra:

- `Usuario` como entidade de autenticação.
- `PerfilUsuario` como enum de roles.
- `Tarefa.responsavel` como `@ManyToOne`.
- `/minhas-demandas` como uma consulta filtrada pelo usuário logado.

Quando `AUTH_REQUIRED=true`, o sistema usa a sessão do Auth.js para descobrir `session.user.id` e buscar apenas tarefas daquele responsável.

## Como debugar

- Erro de tipo: rode `npm run typecheck`.
- Erro de padrão React/Next: rode `npm run lint`.
- Erro de build: rode `npm run build`.
- Erro de banco: confira `DATABASE_URL`, rode `npm run db:push` e abra `npm run db:studio`.
- Erro de validação: leia a resposta JSON da API; ela traz `error.message` e detalhes do Zod.

## Como evoluir

O próximo nível profissional é extrair mais funções de serviço para operações maiores, adicionar testes automatizados, endurecer permissões por perfil, integrar emissão real de boleto/nota fiscal e criar jobs automáticos para reenvio preventivo.
