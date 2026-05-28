import type { StatusTarefa, TipoCliente, Prisma } from "@/generated/prisma/client";
import { demoBoletos, demoClientes, demoCobrancas, demoInteracoes, demoParcelas, demoTarefas } from "@/lib/demo-data";
import { prisma } from "@/lib/server/prisma";

const atrasoStatuses = ["COBRANCA_5_DIAS", "COBRANCA_10_DIAS", "COBRANCA_30_DIAS", "NEGATIVACAO"] as const;

function hojeFimDoDia() {
  const hoje = new Date();
  hoje.setHours(23, 59, 59, 999);
  return hoje;
}

export async function getDashboardData() {
  try {
    const [clientesAtivos, totalClientes, totalCobrancas, parcelasAtrasadas, tarefasPendentes, valorCarteira, proximasTarefas, cobrancasCriticas] =
      await prisma.$transaction([
        prisma.cliente.count({ where: { statusAtivo: true } }),
        prisma.cliente.count(),
        prisma.cobranca.count({ where: { statusCobranca: { notIn: ["PAGA", "CANCELADA"] } } }),
        prisma.parcela.count({ where: { status: { in: ["ATRASADA", "EM_NEGATIVACAO"] } } }),
        prisma.tarefa.count({ where: { status: { in: ["ABERTA", "EM_ANDAMENTO"] }, dataAgendada: { lte: hojeFimDoDia() } } }),
        prisma.cobranca.aggregate({ _sum: { valorTotal: true } }),
        prisma.tarefa.findMany({
          where: { status: { in: ["ABERTA", "EM_ANDAMENTO"] } },
          orderBy: { dataAgendada: "asc" },
          take: 6,
          include: { cliente: true, cobranca: true },
        }),
        prisma.cobranca.findMany({
          where: { statusCobranca: { in: [...atrasoStatuses] } },
          orderBy: { dataVencimentoPrincipal: "asc" },
          take: 6,
          include: { cliente: true, parcelas: { orderBy: { numeroParcela: "asc" } } },
        }),
      ]);

    return {
      usingDemoData: false,
      metricas: {
        clientesAtivos,
        totalClientes,
        totalCobrancas,
        parcelasAtrasadas,
        tarefasPendentes,
        valorCarteira: Number(valorCarteira._sum.valorTotal ?? 0),
      },
      proximasTarefas,
      cobrancasCriticas,
    };
  } catch {
    const valorCarteira = demoCobrancas.reduce((sum, cobranca) => sum + Number(cobranca.valorTotal), 0);

    return {
      usingDemoData: true,
      metricas: {
        clientesAtivos: demoClientes.filter((cliente) => cliente.statusAtivo).length,
        totalClientes: demoClientes.length,
        totalCobrancas: demoCobrancas.length,
        parcelasAtrasadas: demoParcelas.filter((parcela) => ["ATRASADA", "EM_NEGATIVACAO"].includes(parcela.status)).length,
        tarefasPendentes: demoTarefas.filter((tarefa) => tarefa.status === "ABERTA").length,
        valorCarteira,
      },
      proximasTarefas: demoTarefas,
      cobrancasCriticas: demoCobrancas.filter((cobranca) => atrasoStatuses.includes(cobranca.statusCobranca as (typeof atrasoStatuses)[number])),
    };
  }
}

export async function listarClientesPage() {
  try {
    return await prisma.cliente.findMany({
      orderBy: { atualizadoEm: "desc" },
      include: {
        cobrancas: { orderBy: { criadoEm: "desc" }, take: 3, include: { parcelas: true } },
        tarefas: { orderBy: { dataAgendada: "asc" }, take: 2 },
      },
    });
  } catch {
    return demoClientes;
  }
}

export async function listarClientesOptions() {
  try {
    return await prisma.cliente.findMany({
      where: { statusAtivo: true },
      orderBy: { nome: "asc" },
      select: { id: true, nome: true, documento: true },
    });
  } catch {
    return demoClientes.map((cliente) => ({
      id: cliente.id,
      nome: cliente.nome,
      documento: cliente.documento,
    }));
  }
}

export async function buscarClientePage(id: string) {
  try {
    return await prisma.cliente.findUnique({
      where: { id },
      include: {
        cobrancas: {
          orderBy: { criadoEm: "desc" },
          include: {
            parcelas: { orderBy: { numeroParcela: "asc" }, include: { boleto: true } },
            boletos: true,
            tarefas: { orderBy: { dataAgendada: "asc" } },
          },
        },
        interacoes: { orderBy: { dataHora: "desc" } },
        tarefas: { orderBy: { dataAgendada: "asc" } },
      },
    });
  } catch {
    return demoClientes.find((cliente) => cliente.id === id) ?? demoClientes[0] ?? null;
  }
}

export async function listarCobrancasPage() {
  try {
    return await prisma.cobranca.findMany({
      orderBy: { atualizadoEm: "desc" },
      include: { cliente: true, parcelas: { orderBy: { numeroParcela: "asc" } }, tarefas: true },
    });
  } catch {
    return demoCobrancas;
  }
}

export async function buscarCobrancaPage(id: string) {
  try {
    return await prisma.cobranca.findUnique({
      where: { id },
      include: {
        cliente: true,
        parcelas: { orderBy: { numeroParcela: "asc" }, include: { boleto: true } },
        boletos: true,
        interacoes: { orderBy: { dataHora: "desc" } },
        tarefas: { orderBy: { dataAgendada: "asc" } },
      },
    });
  } catch {
    return demoCobrancas.find((cobranca) => cobranca.id === id) ?? demoCobrancas[0] ?? null;
  }
}

export async function listarParcelasPage() {
  try {
    return await prisma.parcela.findMany({
      orderBy: { vencimento: "asc" },
      include: { boleto: true, cobranca: { include: { cliente: true } } },
    });
  } catch {
    return demoParcelas;
  }
}

export async function listarBoletosPage(tipoCliente?: TipoCliente) {
  try {
    return await prisma.boleto.findMany({
      where: tipoCliente
        ? {
            OR: [
              { parcela: { cobranca: { cliente: { tipoCliente } } } },
              { cobranca: { cliente: { tipoCliente } } },
            ],
          }
        : undefined,
      orderBy: { vencimento: "asc" },
      include: { parcela: { include: { cobranca: { include: { cliente: true } } } }, cobranca: { include: { cliente: true } } },
    });
  } catch {
    if (!tipoCliente) {
      return demoBoletos;
    }

    return demoBoletos.filter((boleto) => {
      const cliente = boleto.parcela?.cobranca.cliente ?? boleto.cobranca?.cliente;
      return cliente?.tipoCliente === tipoCliente;
    });
  }
}

export async function listarInteracoesPage() {
  try {
    return await prisma.interacao.findMany({
      orderBy: { dataHora: "desc" },
      take: 80,
      include: { cliente: true, cobranca: true, parcela: true },
    });
  } catch {
    return demoInteracoes;
  }
}

export async function listarTarefasPage() {
  try {
    return await prisma.tarefa.findMany({
      orderBy: { dataAgendada: "asc" },
      include: { cliente: true, cobranca: true },
    });
  } catch {
    return demoTarefas;
  }
}

export async function listarMinhasDemandasPage(usuarioId?: string) {
  try {
    const statusAbertos: StatusTarefa[] = ["ABERTA", "EM_ANDAMENTO", "ATRASADA"];
    const where: Prisma.TarefaWhereInput = usuarioId
      ? { responsavelId: usuarioId, status: { in: statusAbertos } }
      : { status: { in: statusAbertos } };

    return await prisma.tarefa.findMany({
      where,
      orderBy: { dataAgendada: "asc" },
      include: { cliente: true, cobranca: true, responsavel: true },
    });
  } catch {
    return demoTarefas;
  }
}

export async function listarUsuariosPage() {
  try {
    return await prisma.usuario.findMany({
      orderBy: { nome: "asc" },
      include: {
        tarefas: {
          where: { status: { in: ["ABERTA", "EM_ANDAMENTO", "ATRASADA"] } },
          orderBy: { dataAgendada: "asc" },
          include: { cliente: true },
        },
        cobrancasResponsaveis: {
          where: { statusCobranca: { notIn: ["PAGA", "CANCELADA"] } },
        },
      },
    });
  } catch {
    return [
      {
        id: "demo-usuario-financeiro",
        nome: "Setor Financeiro",
        email: "financeiro@apvistoria.local",
        perfil: "FINANCEIRO",
        permissoes: ["clientes:write", "cobrancas:write"],
        ativo: true,
        criadoEm: new Date("2026-05-01T12:00:00.000Z"),
        atualizadoEm: new Date("2026-05-28T12:00:00.000Z"),
        senhaHash: null,
        tarefas: demoTarefas,
        cobrancasResponsaveis: demoCobrancas,
      },
      {
        id: "demo-usuario-operador",
        nome: "Operador de Cobrança",
        email: "operador@apvistoria.local",
        perfil: "OPERADOR",
        permissoes: ["clientes:read", "tarefas:write"],
        ativo: true,
        criadoEm: new Date("2026-05-01T12:00:00.000Z"),
        atualizadoEm: new Date("2026-05-28T12:00:00.000Z"),
        senhaHash: null,
        tarefas: demoTarefas.slice(0, 1),
        cobrancasResponsaveis: [],
      },
    ];
  }
}

export async function listarAuditoriaPage() {
  try {
    return await prisma.auditoria.findMany({
      orderBy: { dataHora: "desc" },
      take: 100,
      include: { usuario: true },
    });
  } catch {
    return [];
  }
}
