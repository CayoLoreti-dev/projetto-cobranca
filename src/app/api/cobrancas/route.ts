import { Prisma } from "@/generated/prisma/client";
import { cobrancaCreateSchema, paginationSchema, statusCobrancaValues } from "@/lib/validations";
import { created, fail, handleRouteError, ok, parseSearchParams } from "@/lib/server/api-response";
import { registrarAuditoria, toAuditJson } from "@/lib/server/audit";
import { getRequestContext } from "@/lib/server/context";
import { prisma } from "@/lib/server/prisma";
import { readJson, emptyToNull } from "@/lib/server/request";
import {
  gerarCodigoBarrasDemo,
  gerarLinhaDigitavelDemo,
  gerarTarefasDeCobranca,
  resolveCobrancaStatus,
} from "@/lib/server/rules";
import { cobrancaInclude, serializeCobranca } from "@/lib/server/serializers";

export const runtime = "nodejs";
export const dynamic = "force-dynamic";

export async function GET(request: Request) {
  try {
    const query = paginationSchema
      .extend({
        clienteId: cobrancaCreateSchema.shape.clienteId.optional(),
        statusCobranca: cobrancaCreateSchema.shape.statusCobranca.optional(),
      })
      .parse(parseSearchParams(request));

    const where: Prisma.CobrancaWhereInput = {
      ...(query.clienteId ? { clienteId: query.clienteId } : {}),
      ...(query.statusCobranca ? { statusCobranca: query.statusCobranca } : {}),
      ...(query.busca
        ? {
            OR: [
              { cliente: { nome: { contains: query.busca, mode: "insensitive" } } },
              { cliente: { documento: { contains: query.busca, mode: "insensitive" } } },
              { observacoes: { contains: query.busca, mode: "insensitive" } },
            ],
          }
        : {}),
    };

    const [cobrancas, total] = await prisma.$transaction([
      prisma.cobranca.findMany({
        where,
        orderBy: { atualizadoEm: "desc" },
        skip: (query.page - 1) * query.pageSize,
        take: query.pageSize,
        include: cobrancaInclude,
      }),
      prisma.cobranca.count({ where }),
    ]);

    return ok({
      items: cobrancas.map(serializeCobranca),
      pagination: { page: query.page, pageSize: query.pageSize, total },
    });
  } catch (error) {
    return handleRouteError(error);
  }
}

export async function POST(request: Request) {
  try {
    const body = await readJson(request);
    const input = cobrancaCreateSchema.parse(body);
    const context = await getRequestContext(request);

    const cobranca = await prisma.$transaction(async (tx) => {
      const cliente = await tx.cliente.findUnique({ where: { id: input.clienteId } });

      if (!cliente) {
        throw new Error("Cliente não encontrado para criar a cobrança.");
      }

      const statusCobranca =
        statusCobrancaValues.includes(input.statusCobranca) && input.statusCobranca !== "EMITIDA"
          ? input.statusCobranca
          : resolveCobrancaStatus(input.dataVencimentoPrincipal);
      const tarefas = gerarTarefasDeCobranca(input.dataVencimentoPrincipal);

      const novaCobranca = await tx.cobranca.create({
        data: {
          clienteId: input.clienteId,
          tipoCobranca: input.tipoCobranca,
          valorTotal: input.valorTotal,
          statusCobranca,
          dataEmissao: input.dataEmissao,
          dataVencimentoPrincipal: input.dataVencimentoPrincipal,
          responsavelAtualId: emptyToNull(input.responsavelAtualId),
          proximaAcao: input.proximaAcao || tarefas[0]?.descricao,
          dataProximaAcao: input.dataProximaAcao ?? tarefas[0]?.dataAgendada,
          observacoes: input.observacoes || null,
          parcelas: {
            create: input.parcelas.map((parcela) => ({
              numeroParcela: parcela.numeroParcela,
              valor: parcela.valor,
              vencimento: parcela.vencimento,
              status: parcela.status,
              observacoes: parcela.observacoes || null,
            })),
          },
        },
        include: { parcelas: { orderBy: { numeroParcela: "asc" } } },
      });

      if (input.gerarBoletos) {
        for (const parcela of novaCobranca.parcelas) {
          await tx.boleto.create({
            data: {
              parcelaId: parcela.id,
              cobrancaId: novaCobranca.id,
              codigoBarras: gerarCodigoBarrasDemo(parcela.numeroParcela),
              linhaDigitavel: gerarLinhaDigitavelDemo(parcela.numeroParcela),
              valor: parcela.valor,
              vencimento: parcela.vencimento,
              status: "EMITIDO",
            },
          });
        }
      }

      await tx.tarefa.createMany({
        data: tarefas.map((tarefa) => ({
          clienteId: cliente.id,
          cobrancaId: novaCobranca.id,
          tipo: tarefa.tipo,
          descricao: tarefa.descricao,
          dataAgendada: tarefa.dataAgendada,
          responsavelId: context.usuarioId,
          responsavelNome: context.usuarioNome,
        })),
      });

      await tx.interacao.create({
        data: {
          clienteId: cliente.id,
          cobrancaId: novaCobranca.id,
          canal: "SISTEMA",
          resultado: "REGISTRO_INTERNO",
          responsavelId: context.usuarioId,
          responsavelNome: context.usuarioNome,
          observacoes: "Cobrança criada com parcelas, tarefas e boletos iniciais.",
        },
      });

      const completa = await tx.cobranca.findUniqueOrThrow({
        where: { id: novaCobranca.id },
        include: cobrancaInclude,
      });

      await registrarAuditoria(tx, context, {
        entidade: "Cobranca",
        entidadeId: completa.id,
        acao: "CRIAR",
        depois: toAuditJson(completa),
      });

      return completa;
    });

    return created(serializeCobranca(cobranca));
  } catch (error) {
    if (error instanceof Prisma.PrismaClientKnownRequestError && error.code === "P2003") {
      return fail("Cliente ou responsável informado não existe.", 400);
    }

    return handleRouteError(error);
  }
}
