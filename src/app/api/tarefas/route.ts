import { Prisma } from "@/generated/prisma/client";
import { paginationSchema, statusTarefaValues, tarefaCreateSchema } from "@/lib/validations";
import { created, handleRouteError, ok, parseSearchParams } from "@/lib/server/api-response";
import { registrarAuditoria, toAuditJson } from "@/lib/server/audit";
import { getRequestContext } from "@/lib/server/context";
import { prisma } from "@/lib/server/prisma";
import { readJson, emptyToNull } from "@/lib/server/request";

export const runtime = "nodejs";
export const dynamic = "force-dynamic";

export async function GET(request: Request) {
  try {
    const query = paginationSchema
      .extend({
        clienteId: paginationSchema.shape.busca.optional(),
        status: paginationSchema.shape.busca.optional(),
      })
      .parse(parseSearchParams(request));
    const status = statusTarefaValues.find((value) => value === query.status);
    const where: Prisma.TarefaWhereInput = {
      ...(query.clienteId ? { clienteId: query.clienteId } : {}),
      ...(status ? { status } : {}),
      ...(query.busca
        ? {
            OR: [
              { cliente: { nome: { contains: query.busca, mode: "insensitive" } } },
              { descricao: { contains: query.busca, mode: "insensitive" } },
              { responsavelNome: { contains: query.busca, mode: "insensitive" } },
            ],
          }
        : {}),
    };

    const [tarefas, total] = await prisma.$transaction([
      prisma.tarefa.findMany({
        where,
        orderBy: { dataAgendada: "asc" },
        skip: (query.page - 1) * query.pageSize,
        take: query.pageSize,
        include: { cliente: true, cobranca: true },
      }),
      prisma.tarefa.count({ where }),
    ]);

    return ok({ items: tarefas, pagination: { page: query.page, pageSize: query.pageSize, total } });
  } catch (error) {
    return handleRouteError(error);
  }
}

export async function POST(request: Request) {
  try {
    const body = await readJson(request);
    const input = tarefaCreateSchema.parse(body);
    const context = await getRequestContext(request);

    const tarefa = await prisma.$transaction(async (tx) => {
      const novaTarefa = await tx.tarefa.create({
        data: {
          clienteId: input.clienteId,
          cobrancaId: emptyToNull(input.cobrancaId),
          tipo: input.tipo,
          descricao: input.descricao,
          dataAgendada: input.dataAgendada,
          status: input.status,
          responsavelId: emptyToNull(input.responsavelId) ?? context.usuarioId,
          responsavelNome: input.responsavelNome,
        },
      });

      await registrarAuditoria(tx, context, {
        entidade: "Tarefa",
        entidadeId: novaTarefa.id,
        acao: "CRIAR",
        depois: toAuditJson(novaTarefa),
      });

      return novaTarefa;
    });

    return created(tarefa);
  } catch (error) {
    return handleRouteError(error);
  }
}
