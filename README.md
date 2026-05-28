# Sistema interno de cobranças - AP Vistoria Predial

Projeto educativo e operacional para cadastro de clientes, cobranças, parcelas, boletos, histórico, agenda, relatórios e auditoria.

## Stack

- Next.js App Router + TypeScript
- Tailwind CSS
- Prisma ORM 7 + PostgreSQL
- Zod para validação
- Auth.js/NextAuth com login por credenciais
- React Hook Form nos formulários
- TanStack Query nas mutações de front-end

## Como rodar

1. Instale dependências:

```bash
npm install
```

2. Configure ambiente:

```bash
copy .env.example .env
```

3. Suba um PostgreSQL. Se você tiver Docker:

```bash
docker compose up -d
```

4. Crie as tabelas e dados iniciais:

```bash
npm run db:push
npm run db:seed
```

5. Rode o app:

```bash
npm run dev
```

Login de desenvolvimento criado pela seed:

- E-mail: `financeiro@apvistoria.local`
- Senha: `apvistoria123`

Por padrão `AUTH_REQUIRED=false`, então a navegação interna fica aberta para facilitar estudo mesmo sem banco local. Para exigir login, altere para `AUTH_REQUIRED=true` depois de rodar o banco e a seed.

## Scripts

- `npm run dev`: servidor local.
- `npm run build`: gera Prisma Client e valida build Next.
- `npm run lint`: valida padrões de código.
- `npm run typecheck`: valida TypeScript.
- `npm run prisma:generate`: gera o client tipado do Prisma.
- `npm run db:push`: cria/atualiza tabelas no PostgreSQL.
- `npm run db:seed`: recria dados de desenvolvimento.
- `npm run db:studio`: abre painel visual do Prisma.

## Organização

- `prisma/schema.prisma`: modelos do banco. Em Java, pense como entidades JPA.
- `prisma/seed.ts`: dados iniciais. Parecido com `data.sql` ou um `CommandLineRunner`.
- `src/app`: rotas, páginas e APIs do Next.js.
- `src/app/api`: back-end HTTP. Parecido com controllers do Spring.
- `src/components`: componentes reutilizáveis de interface.
- `src/components/forms`: formulários com React Hook Form + Zod.
- `src/lib/validations.ts`: schemas Zod. Parecido com DTOs com Bean Validation.
- `src/lib/server/rules.ts`: regras de negócio de cobrança.
- `src/lib/server/prisma.ts`: conexão com banco via Prisma.
- `src/lib/server/audit.ts`: grava auditoria.
- `src/lib/server/queries.ts`: leituras usadas pelas telas.
- `src/lib/constants.ts`: labels e textos de enums.

## Contas do setor e demandas

O sistema agora tem módulo de usuários:

- `/usuarios`: lista pessoas do setor, perfil, status e quantidade de demandas.
- `/usuarios/novo`: cria uma conta interna com senha inicial.
- `/minhas-demandas`: mostra tarefas do usuário logado; se o login estiver desligado, mostra demandas abertas do setor.

Perfis disponíveis:

- `ADMIN`: administra usuários, clientes, cobranças e auditoria.
- `FINANCEIRO`: cria clientes, cobranças e tarefas.
- `OPERADOR`: executa tarefas, registra contatos e acompanha cobranças.
- `LEITURA`: consulta informações sem alterar dados.

Para cada pessoa ver apenas suas demandas, faça:

1. Suba o banco.
2. Rode `npm run db:push` e `npm run db:seed`.
3. Crie os usuários em `/usuarios/novo`.
4. Altere `AUTH_REQUIRED="true"` no `.env`.
5. Atribua tarefas usando o responsável nas APIs/telas de tarefas.

## Fluxo de dados

1. Usuário preenche um formulário.
2. React Hook Form controla estado e erros do formulário.
3. Zod valida os dados no front.
4. O formulário envia JSON para uma rota em `src/app/api`.
5. A rota valida de novo com Zod no servidor.
6. A rota aplica regra de negócio.
7. Prisma grava no PostgreSQL.
8. Alterações importantes criam auditoria e histórico.
9. A tela recarrega dados atualizados.

Validação duplicada no front e no back é intencional: o front melhora a experiência, o back protege o sistema.

## Ponte com Java/Spring

- `schema.prisma` lembra entidades JPA, mas o Prisma gera o client de acesso ao banco.
- `route.ts` lembra `@RestController`.
- Schemas Zod lembram DTOs com validações.
- `src/lib/server/rules.ts` lembra uma camada de service com regra de negócio.
- Enums do Prisma lembram `enum` em Java.
- Relações Prisma (`Cliente` -> `Cobranca` -> `Parcela`) lembram `@OneToMany` e `@ManyToOne`.

## Regras já implementadas

- Cliente com PF, PJ ou condomínio.
- Cobrança à vista com parcela única.
- Cobrança parcelada criada com todas as parcelas.
- Boleto vinculado à parcela/cobrança.
- Histórico de interações por canal.
- Agenda automática: envio, reenvio preventivo, 5, 10 e 30 dias.
- Auditoria em criação, atualização, exclusão e mudança de status.
- Dashboard, relatórios e casos críticos.

## Observação sobre boletos

Os códigos de boleto criados pela aplicação são demonstrativos. Para emissão bancária real será necessário integrar com banco, gateway ou sistema homologado.
