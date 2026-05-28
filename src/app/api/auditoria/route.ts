import { Prisma } from "@/generated/prisma/client";
import { paginationSchema } from "@/lib/validations";
import { handleRouteError, ok, parseSearchParams } from "@/lib/server/api-response";
import { prisma } from "@/lib/server/prisma";

export const runtime = "nodejs";
export const dynamic = "force-dynamic";

export async function GET(request: Request) {
  try {
    const query = paginationSchema
      .extend({
        entidade: paginationSchema.shape.busca.optional(),
        entidadeId: paginationSchema.shape.busca.optional(),
      })
      .parse(parseSearchParams(request));
    const where: Prisma.AuditoriaWhereInput = {
      ...(query.entidade ? { entidade: query.entidade } : {}),
      ...(query.entidadeId ? { entidadeId: query.entidadeId } : {}),
      ...(query.busca
        ? {
            OR: [
              { entidade: { contains: query.busca, mode: "insensitive" } },
              { entidadeId: { contains: query.busca, mode: "insensitive" } },
              { usuarioNome: { contains: query.busca, mode: "insensitive" } },
            ],
          }
        : {}),
    };

    const [auditorias, total] = await prisma.$transaction([
      prisma.auditoria.findMany({
        where,
        orderBy: { dataHora: "desc" },
        skip: (query.page - 1) * query.pageSize,
        take: query.pageSize,
        include: { usuario: true },
      }),
      prisma.auditoria.count({ where }),
    ]);

    return ok({ items: auditorias, pagination: { page: query.page, pageSize: query.pageSize, total } });
  } catch (error) {
    return handleRouteError(error);
  }
}
