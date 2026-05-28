import { Prisma } from "@/generated/prisma/client";
import { paginationSchema, statusParcelaValues } from "@/lib/validations";
import { handleRouteError, ok, parseSearchParams } from "@/lib/server/api-response";
import { prisma } from "@/lib/server/prisma";

export const runtime = "nodejs";
export const dynamic = "force-dynamic";

export async function GET(request: Request) {
  try {
    const query = paginationSchema
      .extend({
        cobrancaId: paginationSchema.shape.busca.optional(),
        status: paginationSchema.shape.busca.optional(),
      })
      .parse(parseSearchParams(request));

    const status = statusParcelaValues.find((value) => value === query.status);
    const where: Prisma.ParcelaWhereInput = {
      ...(query.cobrancaId ? { cobrancaId: query.cobrancaId } : {}),
      ...(status ? { status } : {}),
      ...(query.busca
        ? {
            OR: [
              { cobranca: { cliente: { nome: { contains: query.busca, mode: "insensitive" } } } },
              { cobranca: { cliente: { documento: { contains: query.busca, mode: "insensitive" } } } },
              { observacoes: { contains: query.busca, mode: "insensitive" } },
            ],
          }
        : {}),
    };

    const [parcelas, total] = await prisma.$transaction([
      prisma.parcela.findMany({
        where,
        orderBy: { vencimento: "asc" },
        skip: (query.page - 1) * query.pageSize,
        take: query.pageSize,
        include: { boleto: true, cobranca: { include: { cliente: true } } },
      }),
      prisma.parcela.count({ where }),
    ]);

    return ok({ items: parcelas, pagination: { page: query.page, pageSize: query.pageSize, total } });
  } catch (error) {
    return handleRouteError(error);
  }
}
