import { Prisma } from "@/generated/prisma/client";
import { boletoCreateSchema, paginationSchema, statusBoletoValues, tipoClienteValues } from "@/lib/validations";
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
        status: paginationSchema.shape.busca.optional(),
        tipoCliente: paginationSchema.shape.busca.optional(),
      })
      .parse(parseSearchParams(request));
    const status = statusBoletoValues.find((value) => value === query.status);
    const tipoCliente = tipoClienteValues.find((value) => value === query.tipoCliente);
    const andFilters: Prisma.BoletoWhereInput[] = [];

    if (tipoCliente) {
      andFilters.push({
        OR: [
          { parcela: { cobranca: { cliente: { tipoCliente } } } },
          { cobranca: { cliente: { tipoCliente } } },
        ],
      });
    }

    if (query.busca) {
      andFilters.push({
        OR: [
          { linhaDigitavel: { contains: query.busca, mode: "insensitive" } },
          { codigoBarras: { contains: query.busca, mode: "insensitive" } },
          { parcela: { cobranca: { cliente: { nome: { contains: query.busca, mode: "insensitive" } } } } },
        ],
      });
    }

    const where: Prisma.BoletoWhereInput = {
      ...(status ? { status } : {}),
      ...(andFilters.length > 0 ? { AND: andFilters } : {}),
    };

    const [boletos, total] = await prisma.$transaction([
      prisma.boleto.findMany({
        where,
        orderBy: { vencimento: "asc" },
        skip: (query.page - 1) * query.pageSize,
        take: query.pageSize,
        include: {
          parcela: { include: { cobranca: { include: { cliente: true } } } },
          cobranca: { include: { cliente: true } },
        },
      }),
      prisma.boleto.count({ where }),
    ]);

    return ok({ items: boletos, pagination: { page: query.page, pageSize: query.pageSize, total } });
  } catch (error) {
    return handleRouteError(error);
  }
}

export async function POST(request: Request) {
  try {
    const body = await readJson(request);
    const input = boletoCreateSchema.parse(body);
    const context = await getRequestContext(request);

    const boleto = await prisma.$transaction(async (tx) => {
      const novoBoleto = await tx.boleto.create({
        data: {
          parcelaId: emptyToNull(input.parcelaId),
          cobrancaId: emptyToNull(input.cobrancaId),
          codigoBarras: input.codigoBarras,
          linhaDigitavel: input.linhaDigitavel,
          valor: input.valor,
          vencimento: input.vencimento,
          status: input.status,
          dataEnvio: input.dataEnvio,
          confirmacaoLeitura: input.confirmacaoLeitura,
          confirmacaoRecebimento: input.confirmacaoRecebimento,
          pdfUrl: input.pdfUrl || null,
          observacoes: input.observacoes || null,
        },
      });

      await registrarAuditoria(tx, context, {
        entidade: "Boleto",
        entidadeId: novoBoleto.id,
        acao: "CRIAR",
        depois: toAuditJson(novoBoleto),
      });

      return novoBoleto;
    });

    return created(boleto);
  } catch (error) {
    return handleRouteError(error);
  }
}
