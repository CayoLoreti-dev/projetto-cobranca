import { parcelaUpdateSchema } from "@/lib/validations";
import { fail, handleRouteError, ok } from "@/lib/server/api-response";
import { registrarAuditoria, toAuditJson } from "@/lib/server/audit";
import { getRequestContext } from "@/lib/server/context";
import { prisma } from "@/lib/server/prisma";
import { readJson } from "@/lib/server/request";

export const runtime = "nodejs";
export const dynamic = "force-dynamic";

type RouteContext = { params: Promise<{ id: string }> | { id: string } };

export async function PATCH(request: Request, { params }: RouteContext) {
  try {
    const { id } = await params;
    const body = await readJson(request);
    const input = parcelaUpdateSchema.parse(body);
    const context = await getRequestContext(request);

    const parcela = await prisma.$transaction(async (tx) => {
      const antes = await tx.parcela.findUnique({
        where: { id },
        include: { cobranca: true, boleto: true },
      });

      if (!antes) {
        return null;
      }

      const atualizada = await tx.parcela.update({
        where: { id },
        data: {
          ...(input.numeroParcela ? { numeroParcela: input.numeroParcela } : {}),
          ...(input.valor ? { valor: input.valor } : {}),
          ...(input.vencimento ? { vencimento: input.vencimento } : {}),
          ...(input.status ? { status: input.status } : {}),
          ...(input.observacoes !== undefined ? { observacoes: input.observacoes || null } : {}),
          ...(input.dataEnvio !== undefined ? { dataEnvio: input.dataEnvio } : {}),
          ...(input.dataReenvio !== undefined ? { dataReenvio: input.dataReenvio } : {}),
          ...(input.dataPagamento !== undefined ? { dataPagamento: input.dataPagamento } : {}),
        },
        include: { cobranca: true, boleto: true },
      });

      if (input.status && input.status !== antes.status) {
        await tx.interacao.create({
          data: {
            clienteId: atualizada.cobranca.clienteId,
            cobrancaId: atualizada.cobrancaId,
            parcelaId: atualizada.id,
            canal: "SISTEMA",
            resultado: "REGISTRO_INTERNO",
            responsavelId: context.usuarioId,
            responsavelNome: context.usuarioNome,
            observacoes: `Status da parcela ${atualizada.numeroParcela} alterado de ${antes.status} para ${atualizada.status}.`,
          },
        });
      }

      await registrarAuditoria(tx, context, {
        entidade: "Parcela",
        entidadeId: id,
        acao: input.status && input.status !== antes.status ? "ALTERAR_STATUS" : "ATUALIZAR",
        antes: toAuditJson(antes),
        depois: toAuditJson(atualizada),
      });

      return atualizada;
    });

    if (!parcela) {
      return fail("Parcela não encontrada.", 404);
    }

    return ok(parcela);
  } catch (error) {
    return handleRouteError(error);
  }
}
