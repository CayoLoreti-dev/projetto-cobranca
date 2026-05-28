import { tarefaUpdateSchema } from "@/lib/validations";
import { fail, handleRouteError, ok } from "@/lib/server/api-response";
import { registrarAuditoria, toAuditJson } from "@/lib/server/audit";
import { getRequestContext } from "@/lib/server/context";
import { prisma } from "@/lib/server/prisma";
import { readJson, emptyToNull } from "@/lib/server/request";

export const runtime = "nodejs";
export const dynamic = "force-dynamic";

type RouteContext = { params: Promise<{ id: string }> | { id: string } };

export async function PATCH(request: Request, { params }: RouteContext) {
  try {
    const { id } = await params;
    const body = await readJson(request);
    const input = tarefaUpdateSchema.parse(body);
    const context = await getRequestContext(request);

    const tarefa = await prisma.$transaction(async (tx) => {
      const antes = await tx.tarefa.findUnique({ where: { id } });

      if (!antes) {
        return null;
      }

      const atualizada = await tx.tarefa.update({
        where: { id },
        data: {
          ...(input.clienteId ? { clienteId: input.clienteId } : {}),
          ...(input.cobrancaId !== undefined ? { cobrancaId: emptyToNull(input.cobrancaId) } : {}),
          ...(input.tipo ? { tipo: input.tipo } : {}),
          ...(input.descricao ? { descricao: input.descricao } : {}),
          ...(input.dataAgendada ? { dataAgendada: input.dataAgendada } : {}),
          ...(input.status ? { status: input.status } : {}),
          ...(input.responsavelId !== undefined ? { responsavelId: emptyToNull(input.responsavelId) } : {}),
          ...(input.responsavelNome ? { responsavelNome: input.responsavelNome } : {}),
          ...(input.concluidaEm !== undefined ? { concluidaEm: input.concluidaEm } : {}),
        },
      });

      await registrarAuditoria(tx, context, {
        entidade: "Tarefa",
        entidadeId: id,
        acao: input.status && input.status !== antes.status ? "ALTERAR_STATUS" : "ATUALIZAR",
        antes: toAuditJson(antes),
        depois: toAuditJson(atualizada),
      });

      return atualizada;
    });

    if (!tarefa) {
      return fail("Tarefa não encontrada.", 404);
    }

    return ok(tarefa);
  } catch (error) {
    return handleRouteError(error);
  }
}
