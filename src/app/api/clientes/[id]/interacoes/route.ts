import { interacaoCreateSchema } from "@/lib/validations";
import { created, fail, handleRouteError, ok } from "@/lib/server/api-response";
import { registrarAuditoria, toAuditJson } from "@/lib/server/audit";
import { getRequestContext } from "@/lib/server/context";
import { prisma } from "@/lib/server/prisma";
import { readJson, emptyToNull } from "@/lib/server/request";

export const runtime = "nodejs";
export const dynamic = "force-dynamic";

type RouteContext = { params: Promise<{ id: string }> | { id: string } };

export async function GET(_request: Request, { params }: RouteContext) {
  try {
    const { id } = await params;
    const cliente = await prisma.cliente.findUnique({
      where: { id },
      select: {
        id: true,
        interacoes: { orderBy: { dataHora: "desc" } },
      },
    });

    if (!cliente) {
      return fail("Cliente não encontrado.", 404);
    }

    return ok(cliente.interacoes);
  } catch (error) {
    return handleRouteError(error);
  }
}

export async function POST(request: Request, { params }: RouteContext) {
  try {
    const { id } = await params;
    const body = await readJson(request);
    const input = interacaoCreateSchema.parse({ ...body, clienteId: id });
    const context = await getRequestContext(request);

    const interacao = await prisma.$transaction(async (tx) => {
      const cliente = await tx.cliente.findUnique({ where: { id } });

      if (!cliente) {
        return null;
      }

      const novaInteracao = await tx.interacao.create({
        data: {
          clienteId: id,
          cobrancaId: emptyToNull(input.cobrancaId),
          parcelaId: emptyToNull(input.parcelaId),
          canal: input.canal,
          resultado: input.resultado,
          responsavelId: emptyToNull(input.responsavelId) ?? context.usuarioId,
          responsavelNome: input.responsavelNome,
          dataHora: input.dataHora,
          observacoes: input.observacoes,
        },
      });

      await registrarAuditoria(tx, context, {
        entidade: "Interacao",
        entidadeId: novaInteracao.id,
        acao: "CRIAR",
        depois: toAuditJson(novaInteracao),
      });

      return novaInteracao;
    });

    if (!interacao) {
      return fail("Cliente não encontrado.", 404);
    }

    return created(interacao);
  } catch (error) {
    return handleRouteError(error);
  }
}
