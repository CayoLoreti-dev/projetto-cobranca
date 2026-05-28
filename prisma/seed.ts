import "dotenv/config";
import { PrismaPg } from "@prisma/adapter-pg";
import { hash } from "bcryptjs";
import { PrismaClient } from "../src/generated/prisma/client";
import {
  gerarCodigoBarrasDemo,
  gerarLinhaDigitavelDemo,
  gerarTarefasDeCobranca,
} from "../src/lib/server/rules";

const adapter = new PrismaPg({ connectionString: `${process.env.DATABASE_URL}` });
const prisma = new PrismaClient({ adapter });

const date = (value: string) => new Date(`${value}T12:00:00-03:00`);

async function main() {
  await prisma.auditoria.deleteMany();
  await prisma.boleto.deleteMany();
  await prisma.interacao.deleteMany();
  await prisma.tarefa.deleteMany();
  await prisma.parcela.deleteMany();
  await prisma.cobranca.deleteMany();
  await prisma.cliente.deleteMany();
  await prisma.usuario.deleteMany();

  const senhaHash = await hash("apvistoria123", 10);
  const financeiro = await prisma.usuario.create({
    data: {
      nome: "Setor Financeiro",
      email: "financeiro@apvistoria.local",
      senhaHash,
      perfil: "FINANCEIRO",
      permissoes: ["clientes:write", "cobrancas:write", "auditoria:read"],
    },
  });

  const operador = await prisma.usuario.create({
    data: {
      nome: "Edivaldo Santos",
      email: "edivaldo@apvistoria.local",
      senhaHash,
      perfil: "OPERADOR",
      permissoes: ["clientes:read", "cobrancas:write"],
    },
  });

  await criarCarteiraCondominio(financeiro.id);
  await criarCarteiraPessoaFisica(operador.id);
  await criarCarteiraEmpresa(financeiro.id);

  await prisma.auditoria.create({
    data: {
      entidade: "Seed",
      entidadeId: "desenvolvimento",
      acao: "CRIAR",
      usuarioId: financeiro.id,
      usuarioNome: financeiro.nome,
      origem: "SISTEMA",
      depois: {
        mensagem: "Base inicial criada para desenvolvimento.",
        login: "financeiro@apvistoria.local",
        senha: "apvistoria123",
      },
    },
  });
}

async function criarCarteiraCondominio(responsavelId: string) {
  const cliente = await prisma.cliente.create({
    data: {
      nome: "Condomínio Residencial Atlântico",
      tipoCliente: "CONDOMINIO",
      documento: "12.345.678/0001-90",
      responsavelFinanceiro: "Larissa Menezes",
      email: "financeiro@atlantico.com.br",
      telefone: "(21) 98888-1000",
      whatsapp: "(21) 98888-1000",
      endereco: "Rua das Palmeiras, 120 - Rio de Janeiro/RJ",
      observacoes: "Cliente parcelado com reenvio preventivo ativo.",
    },
  });

  const cobranca = await prisma.cobranca.create({
    data: {
      clienteId: cliente.id,
      tipoCobranca: "PARCELADO",
      valorTotal: 12840.5,
      statusCobranca: "PREVENTIVA",
      dataEmissao: date("2026-05-01"),
      dataVencimentoPrincipal: date("2026-06-02"),
      responsavelAtualId: responsavelId,
      proximaAcao: "Reenviar parcela 2 e confirmar recebimento.",
      dataProximaAcao: date("2026-05-29"),
      observacoes: "Parcelamento aprovado em 3 vezes.",
      parcelas: {
        create: [
          { numeroParcela: 1, valor: 4280.17, vencimento: date("2026-05-15"), status: "PAGA", dataPagamento: date("2026-05-15") },
          { numeroParcela: 2, valor: 4280.17, vencimento: date("2026-06-02"), status: "ENVIADA", dataEnvio: date("2026-05-27") },
          { numeroParcela: 3, valor: 4280.16, vencimento: date("2026-07-02"), status: "PENDENTE" },
        ],
      },
    },
    include: { parcelas: true },
  });

  await criarBoletos(cobranca.id, cobranca.parcelas);
  await criarTarefas(cliente.id, cobranca.id, responsavelId, date("2026-06-02"));

  await prisma.interacao.createMany({
    data: [
      {
        clienteId: cliente.id,
        cobrancaId: cobranca.id,
        canal: "EMAIL",
        resultado: "ENVIADO",
        responsavelId,
        responsavelNome: "Setor Financeiro",
        dataHora: date("2026-05-27"),
        observacoes: "Boleto enviado com confirmação de leitura solicitada.",
      },
      {
        clienteId: cliente.id,
        cobrancaId: cobranca.id,
        canal: "WHATSAPP",
        resultado: "RECEBIDO",
        responsavelId,
        responsavelNome: "Larissa Menezes",
        dataHora: new Date("2026-05-27T13:34:00-03:00"),
        observacoes: "Mensagem recebida pelo responsável financeiro.",
      },
    ],
  });
}

async function criarCarteiraPessoaFisica(responsavelId: string) {
  const cliente = await prisma.cliente.create({
    data: {
      nome: "Marcos Vinicius Almeida",
      tipoCliente: "PF",
      documento: "123.456.789-00",
      responsavelFinanceiro: "Marcos Almeida",
      email: "marcos.almeida@email.com",
      telefone: "(21) 99999-2000",
      whatsapp: "(21) 99999-2000",
      observacoes: "Cobrança à vista em atraso de 10 dias.",
    },
  });

  const cobranca = await prisma.cobranca.create({
    data: {
      clienteId: cliente.id,
      tipoCobranca: "AVISTA",
      valorTotal: 3150,
      statusCobranca: "COBRANCA_10_DIAS",
      dataEmissao: date("2026-05-05"),
      dataVencimentoPrincipal: date("2026-05-18"),
      responsavelAtualId: responsavelId,
      proximaAcao: "Ligar para confirmar intenção de pagamento.",
      dataProximaAcao: date("2026-05-28"),
      parcelas: {
        create: [{ numeroParcela: 1, valor: 3150, vencimento: date("2026-05-18"), status: "ATRASADA" }],
      },
    },
    include: { parcelas: true },
  });

  await criarBoletos(cobranca.id, cobranca.parcelas);
  await criarTarefas(cliente.id, cobranca.id, responsavelId, date("2026-05-18"));

  await prisma.interacao.createMany({
    data: [
      {
        clienteId: cliente.id,
        cobrancaId: cobranca.id,
        canal: "EMAIL",
        resultado: "SEM_RESPOSTA",
        responsavelId,
        responsavelNome: "Setor Financeiro",
        dataHora: date("2026-05-23"),
        observacoes: "Cobrança de 5 dias enviada com prioridade.",
      },
      {
        clienteId: cliente.id,
        cobrancaId: cobranca.id,
        canal: "LIGACAO",
        resultado: "SEM_RESPOSTA",
        responsavelId,
        responsavelNome: "Edivaldo Santos",
        dataHora: new Date("2026-05-26T14:15:00-03:00"),
        observacoes: "Tentativa de contato sem retorno.",
      },
    ],
  });
}

async function criarCarteiraEmpresa(responsavelId: string) {
  const cliente = await prisma.cliente.create({
    data: {
      nome: "Norte Engenharia e Serviços Ltda.",
      tipoCliente: "PJ",
      documento: "45.667.891/0001-54",
      responsavelFinanceiro: "Camila Rocha",
      email: "financeiro@norteengenharia.com.br",
      telefone: "(21) 97777-3000",
      whatsapp: "(21) 97777-3000",
      endereco: "Av. Rio Branco, 500 - Rio de Janeiro/RJ",
      observacoes: "Caso crítico de cobrança formal.",
    },
  });

  const cobranca = await prisma.cobranca.create({
    data: {
      clienteId: cliente.id,
      tipoCobranca: "PARCELADO",
      valorTotal: 16800,
      statusCobranca: "COBRANCA_30_DIAS",
      dataEmissao: date("2026-04-20"),
      dataVencimentoPrincipal: date("2026-04-27"),
      responsavelAtualId: responsavelId,
      proximaAcao: "Preparar evidências e negativação.",
      dataProximaAcao: date("2026-05-28"),
      observacoes: "Cliente ciente da dívida, sem previsão confirmada.",
      parcelas: {
        create: [
          { numeroParcela: 1, valor: 5600, vencimento: date("2026-04-27"), status: "EM_NEGATIVACAO" },
          { numeroParcela: 2, valor: 5600, vencimento: date("2026-05-27"), status: "ATRASADA" },
          { numeroParcela: 3, valor: 5600, vencimento: date("2026-06-27"), status: "PENDENTE" },
        ],
      },
    },
    include: { parcelas: true },
  });

  await criarBoletos(cobranca.id, cobranca.parcelas);
  await criarTarefas(cliente.id, cobranca.id, responsavelId, date("2026-04-27"));

  await prisma.interacao.create({
    data: {
      clienteId: cliente.id,
      cobrancaId: cobranca.id,
      canal: "LIGACAO",
      resultado: "SEM_RESPOSTA",
      responsavelId,
      responsavelNome: "Edivaldo Santos",
      dataHora: new Date("2026-05-27T18:05:00-03:00"),
      observacoes: "Última tentativa antes da medida formal.",
    },
  });
}

async function criarBoletos(
  cobrancaId: string,
  parcelas: Array<{ id: string; numeroParcela: number; valor: unknown; vencimento: Date }>,
) {
  for (const parcela of parcelas) {
    await prisma.boleto.create({
      data: {
        parcelaId: parcela.id,
        cobrancaId,
        codigoBarras: gerarCodigoBarrasDemo(parcela.numeroParcela),
        linhaDigitavel: gerarLinhaDigitavelDemo(parcela.numeroParcela),
        valor: Number(parcela.valor),
        vencimento: parcela.vencimento,
        status: parcela.numeroParcela === 1 ? "ENVIADO" : "EMITIDO",
        dataEnvio: parcela.numeroParcela === 1 ? date("2026-05-27") : null,
        confirmacaoLeitura: parcela.numeroParcela === 1,
      },
    });
  }
}

async function criarTarefas(clienteId: string, cobrancaId: string, responsavelId: string, vencimento: Date) {
  const tarefas = gerarTarefasDeCobranca(vencimento);
  await prisma.tarefa.createMany({
    data: tarefas.map((tarefa) => ({
      clienteId,
      cobrancaId,
      tipo: tarefa.tipo,
      descricao: tarefa.descricao,
      dataAgendada: tarefa.dataAgendada,
      responsavelId,
      responsavelNome: "Setor Financeiro",
      status: tarefa.dataAgendada < new Date("2026-05-28T23:59:59-03:00") ? "ATRASADA" : "ABERTA",
    })),
  });
}

main()
  .then(async () => {
    await prisma.$disconnect();
  })
  .catch(async (error) => {
    console.error(error);
    await prisma.$disconnect();
    process.exit(1);
  });
