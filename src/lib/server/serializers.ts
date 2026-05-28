import type { Prisma } from "@/generated/prisma/client";

export const clienteInclude = {
  cobrancas: {
    orderBy: { criadoEm: "desc" },
    include: {
      parcelas: { orderBy: { numeroParcela: "asc" }, include: { boleto: true } },
      boletos: true,
      responsavelAtual: true,
    },
  },
  interacoes: { orderBy: { dataHora: "desc" }, take: 8 },
  tarefas: { orderBy: { dataAgendada: "asc" }, take: 8 },
} satisfies Prisma.ClienteInclude;

export const cobrancaInclude = {
  cliente: true,
  responsavelAtual: true,
  parcelas: { orderBy: { numeroParcela: "asc" }, include: { boleto: true } },
  boletos: true,
  interacoes: { orderBy: { dataHora: "desc" }, take: 10 },
  tarefas: { orderBy: { dataAgendada: "asc" } },
} satisfies Prisma.CobrancaInclude;

type ClientePayload = Prisma.ClienteGetPayload<{ include: typeof clienteInclude }>;
type CobrancaPayload = Prisma.CobrancaGetPayload<{ include: typeof cobrancaInclude }>;

function decimalToNumber(value: Prisma.Decimal | number | string) {
  return Number(value);
}

function dateToIso(value?: Date | null) {
  return value?.toISOString() ?? null;
}

export function serializeCliente(cliente: ClientePayload) {
  return {
    ...cliente,
    criadoEm: dateToIso(cliente.criadoEm),
    atualizadoEm: dateToIso(cliente.atualizadoEm),
    cobrancas: cliente.cobrancas.map((cobranca) => ({
      ...cobranca,
      valorTotal: decimalToNumber(cobranca.valorTotal),
      dataEmissao: dateToIso(cobranca.dataEmissao),
      dataVencimentoPrincipal: dateToIso(cobranca.dataVencimentoPrincipal),
      dataProximaAcao: dateToIso(cobranca.dataProximaAcao),
      criadoEm: dateToIso(cobranca.criadoEm),
      atualizadoEm: dateToIso(cobranca.atualizadoEm),
      parcelas: cobranca.parcelas.map((parcela) => ({
        ...parcela,
        valor: decimalToNumber(parcela.valor),
        vencimento: dateToIso(parcela.vencimento),
        dataEnvio: dateToIso(parcela.dataEnvio),
        dataReenvio: dateToIso(parcela.dataReenvio),
        dataPagamento: dateToIso(parcela.dataPagamento),
        boleto: parcela.boleto
          ? {
              ...parcela.boleto,
              valor: decimalToNumber(parcela.boleto.valor),
              vencimento: dateToIso(parcela.boleto.vencimento),
              dataEmissao: dateToIso(parcela.boleto.dataEmissao),
              dataEnvio: dateToIso(parcela.boleto.dataEnvio),
            }
          : null,
      })),
      boletos: cobranca.boletos.map((boleto) => ({
        ...boleto,
        valor: decimalToNumber(boleto.valor),
        vencimento: dateToIso(boleto.vencimento),
        dataEmissao: dateToIso(boleto.dataEmissao),
        dataEnvio: dateToIso(boleto.dataEnvio),
      })),
    })),
    interacoes: cliente.interacoes.map((interacao) => ({
      ...interacao,
      dataHora: dateToIso(interacao.dataHora),
    })),
    tarefas: cliente.tarefas.map((tarefa) => ({
      ...tarefa,
      dataAgendada: dateToIso(tarefa.dataAgendada),
      concluidaEm: dateToIso(tarefa.concluidaEm),
      criadoEm: dateToIso(tarefa.criadoEm),
      atualizadoEm: dateToIso(tarefa.atualizadoEm),
    })),
  };
}

export function serializeCobranca(cobranca: CobrancaPayload) {
  return {
    ...cobranca,
    valorTotal: decimalToNumber(cobranca.valorTotal),
    dataEmissao: dateToIso(cobranca.dataEmissao),
    dataVencimentoPrincipal: dateToIso(cobranca.dataVencimentoPrincipal),
    dataProximaAcao: dateToIso(cobranca.dataProximaAcao),
    criadoEm: dateToIso(cobranca.criadoEm),
    atualizadoEm: dateToIso(cobranca.atualizadoEm),
    parcelas: cobranca.parcelas.map((parcela) => ({
      ...parcela,
      valor: decimalToNumber(parcela.valor),
      vencimento: dateToIso(parcela.vencimento),
      dataEnvio: dateToIso(parcela.dataEnvio),
      dataReenvio: dateToIso(parcela.dataReenvio),
      dataPagamento: dateToIso(parcela.dataPagamento),
      boleto: parcela.boleto
        ? {
            ...parcela.boleto,
            valor: decimalToNumber(parcela.boleto.valor),
            vencimento: dateToIso(parcela.boleto.vencimento),
            dataEmissao: dateToIso(parcela.boleto.dataEmissao),
            dataEnvio: dateToIso(parcela.boleto.dataEnvio),
          }
        : null,
    })),
    boletos: cobranca.boletos.map((boleto) => ({
      ...boleto,
      valor: decimalToNumber(boleto.valor),
      vencimento: dateToIso(boleto.vencimento),
      dataEmissao: dateToIso(boleto.dataEmissao),
      dataEnvio: dateToIso(boleto.dataEnvio),
    })),
    interacoes: cobranca.interacoes.map((interacao) => ({
      ...interacao,
      dataHora: dateToIso(interacao.dataHora),
    })),
    tarefas: cobranca.tarefas.map((tarefa) => ({
      ...tarefa,
      dataAgendada: dateToIso(tarefa.dataAgendada),
      concluidaEm: dateToIso(tarefa.concluidaEm),
      criadoEm: dateToIso(tarefa.criadoEm),
      atualizadoEm: dateToIso(tarefa.atualizadoEm),
    })),
  };
}
