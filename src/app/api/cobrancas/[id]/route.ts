import { cobrancaUpdateSchema } from "@/lib/validations";
import { fail, handleRouteError, ok } from "@/lib/server/api-response";
import { registrarAuditoria, toAuditJson } from "@/lib/server/audit";
import { getRequestContext } from "@/lib/server/context";
import { prisma } from "@/lib/server/prisma";
import { readJson, emptyToNull } from "@/lib/server/request";
import { cobrancaInclude, serializeCobranca } from "@/lib/server/serializers";

export const runtime = "nodejs";
export const dynamic = "force-dynamic";

type RouteContext = { params: Promise<{ id: string }> | { id: string } };

export async function GET(_request: Request, { params }: RouteContext) {
  try {
    const { id } = await params;
    const cobranca = await prisma.cobranca.findUnique({
      where: { id },
      include: cobrancaInclude,
    });

    if (!cobranca) {
      return fail("Cobrança não encontrada.", 404);
    }

    return ok(serializeCobranca(cobranca));
  } catch (error) {
    return handleRouteError(error);
  }
}

export async function PATCH(request: Request, { params }: RouteContext) {
  try {
    const { id } = await params;
    const body = await readJson(request);
    const input = cobrancaUpdateSchema.parse(body);
    const context = await getRequestContext(request);

    const cobranca = await prisma.$transaction(async (tx) => {
      const antes = await tx.cobranca.findUnique({ where: { id }, include: cobrancaInclude });

      if (!antes) {
        return null;
      }

      const mudouStatus = input.statusCobranca && input.statusCobranca !== antes.statusCobranca;
      const atualizada = await tx.cobranca.update({
        where: { id },
        data: {
          ...(input.statusCobranca ? { statusCobranca: input.statusCobranca } : {}),
          ...(input.responsavelAtualId !== undefined
            ? { responsavelAtualId: emptyToNull(input.responsavelAtualId) }
            : {}),
          ...(input.proximaAcao !== undefined ? { proximaAcao: input.proximaAcao || null } : {}),
          ...(input.dataProximaAcao ? { dataProximaAcao: input.dataProximaAcao } : {}),
          ...(input.observacoes !== undefined ? { observacoes: input.observacoes || null } : {}),
        },
        include: cobrancaInclude,
      });

      if (mudouStatus) {
        await tx.interacao.create({
          data: {
            clienteId: atualizada.clienteId,
            cobrancaId: atualizada.id,
            canal: "SISTEMA",
            resultado: "REGISTRO_INTERNO",
            responsavelId: context.usuarioId,
            responsavelNome: context.usuarioNome,
            observacoes: `Status alterado de ${antes.statusCobranca} para ${atualizada.statusCobranca}.`,
          },
        });
      }

      await registrarAuditoria(tx, context, {
        entidade: "Cobranca",
        entidadeId: id,
        acao: mudouStatus ? "ALTERAR_STATUS" : "ATUALIZAR",
        antes: toAuditJson(antes),
        depois: toAuditJson(atualizada),
      });

      return atualizada;
    });

    if (!cobranca) {
      return fail("Cobrança não encontrada.", 404);
    }

    return ok(serializeCobranca(cobranca));
  } catch (error) {
    return handleRouteError(error);
  }
}
