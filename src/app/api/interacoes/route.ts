import { Prisma } from "@/generated/prisma/client";
import { canalInteracaoValues, interacaoCreateSchema, paginationSchema } from "@/lib/validations";
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
        cobrancaId: paginationSchema.shape.busca.optional(),
        canal: paginationSchema.shape.busca.optional(),
      })
      .parse(parseSearchParams(request));
    const canal = canalInteracaoValues.find((value) => value === query.canal);
    const where: Prisma.InteracaoWhereInput = {
      ...(query.clienteId ? { clienteId: query.clienteId } : {}),
      ...(query.cobrancaId ? { cobrancaId: query.cobrancaId } : {}),
      ...(canal ? { canal } : {}),
      ...(query.busca
        ? {
            OR: [
              { cliente: { nome: { contains: query.busca, mode: "insensitive" } } },
              { responsavelNome: { contains: query.busca, mode: "insensitive" } },
              { observacoes: { contains: query.busca, mode: "insensitive" } },
            ],
          }
        : {}),
    };

    const [interacoes, total] = await prisma.$transaction([
      prisma.interacao.findMany({
        where,
        orderBy: { dataHora: "desc" },
        skip: (query.page - 1) * query.pageSize,
        take: query.pageSize,
        include: { cliente: true, cobranca: true, parcela: true },
      }),
      prisma.interacao.count({ where }),
    ]);

    return ok({ items: interacoes, pagination: { page: query.page, pageSize: query.pageSize, total } });
  } catch (error) {
    return handleRouteError(error);
  }
}

export async function POST(request: Request) {
  try {
    const body = await readJson(request);
    const input = interacaoCreateSchema.parse(body);
    const context = await getRequestContext(request);

    const interacao = await prisma.$transaction(async (tx) => {
      const novaInteracao = await tx.interacao.create({
        data: {
          clienteId: input.clienteId,
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

    return created(interacao);
  } catch (error) {
    return handleRouteError(error);
  }
}
