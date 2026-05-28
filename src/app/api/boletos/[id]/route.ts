import { boletoUpdateSchema } from "@/lib/validations";
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
    const input = boletoUpdateSchema.parse(body);
    const context = await getRequestContext(request);

    const boleto = await prisma.$transaction(async (tx) => {
      const antes = await tx.boleto.findUnique({ where: { id } });

      if (!antes) {
        return null;
      }

      const atualizado = await tx.boleto.update({
        where: { id },
        data: {
          ...(input.parcelaId !== undefined ? { parcelaId: emptyToNull(input.parcelaId) } : {}),
          ...(input.cobrancaId !== undefined ? { cobrancaId: emptyToNull(input.cobrancaId) } : {}),
          ...(input.codigoBarras ? { codigoBarras: input.codigoBarras } : {}),
          ...(input.linhaDigitavel ? { linhaDigitavel: input.linhaDigitavel } : {}),
          ...(input.valor ? { valor: input.valor } : {}),
          ...(input.vencimento ? { vencimento: input.vencimento } : {}),
          ...(input.status ? { status: input.status } : {}),
          ...(input.dataEnvio !== undefined ? { dataEnvio: input.dataEnvio } : {}),
          ...(typeof input.confirmacaoLeitura === "boolean"
            ? { confirmacaoLeitura: input.confirmacaoLeitura }
            : {}),
          ...(typeof input.confirmacaoRecebimento === "boolean"
            ? { confirmacaoRecebimento: input.confirmacaoRecebimento }
            : {}),
          ...(input.pdfUrl !== undefined ? { pdfUrl: input.pdfUrl || null } : {}),
          ...(input.observacoes !== undefined ? { observacoes: input.observacoes || null } : {}),
        },
      });

      await registrarAuditoria(tx, context, {
        entidade: "Boleto",
        entidadeId: id,
        acao: input.status && input.status !== antes.status ? "ALTERAR_STATUS" : "ATUALIZAR",
        antes: toAuditJson(antes),
        depois: toAuditJson(atualizado),
      });

      return atualizado;
    });

    if (!boleto) {
      return fail("Boleto não encontrado.", 404);
    }

    return ok(boleto);
  } catch (error) {
    return handleRouteError(error);
  }
}
