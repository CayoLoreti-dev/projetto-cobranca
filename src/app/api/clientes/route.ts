import { Prisma } from "@/generated/prisma/client";
import { clienteCreateSchema, paginationSchema } from "@/lib/validations";
import { created, fail, handleRouteError, ok, parseSearchParams } from "@/lib/server/api-response";
import { registrarAuditoria, toAuditJson } from "@/lib/server/audit";
import { getRequestContext } from "@/lib/server/context";
import { prisma } from "@/lib/server/prisma";
import { readJson } from "@/lib/server/request";
import { clienteInclude, serializeCliente } from "@/lib/server/serializers";

export const runtime = "nodejs";
export const dynamic = "force-dynamic";

export async function GET(request: Request) {
  try {
    const query = paginationSchema
      .extend({
        tipoCliente: clienteCreateSchema.shape.tipoCliente.optional(),
        statusAtivo: clienteCreateSchema.shape.statusAtivo.optional(),
      })
      .parse(parseSearchParams(request));

    const where: Prisma.ClienteWhereInput = {
      ...(query.tipoCliente ? { tipoCliente: query.tipoCliente } : {}),
      ...(typeof query.statusAtivo === "boolean" ? { statusAtivo: query.statusAtivo } : {}),
      ...(query.busca
        ? {
            OR: [
              { nome: { contains: query.busca, mode: "insensitive" } },
              { documento: { contains: query.busca, mode: "insensitive" } },
              { email: { contains: query.busca, mode: "insensitive" } },
              { telefone: { contains: query.busca, mode: "insensitive" } },
              { whatsapp: { contains: query.busca, mode: "insensitive" } },
            ],
          }
        : {}),
    };

    const [clientes, total] = await prisma.$transaction([
      prisma.cliente.findMany({
        where,
        orderBy: { atualizadoEm: "desc" },
        skip: (query.page - 1) * query.pageSize,
        take: query.pageSize,
        include: clienteInclude,
      }),
      prisma.cliente.count({ where }),
    ]);

    return ok({
      items: clientes.map(serializeCliente),
      pagination: {
        page: query.page,
        pageSize: query.pageSize,
        total,
      },
    });
  } catch (error) {
    return handleRouteError(error);
  }
}

export async function POST(request: Request) {
  try {
    const body = await readJson(request);
    const input = clienteCreateSchema.parse(body);
    const context = await getRequestContext(request);

    const cliente = await prisma.$transaction(async (tx) => {
      const novoCliente = await tx.cliente.create({
        data: {
          nome: input.nome,
          tipoCliente: input.tipoCliente,
          documento: input.documento,
          responsavelFinanceiro: input.responsavelFinanceiro,
          email: input.email,
          telefone: input.telefone,
          whatsapp: input.whatsapp || null,
          endereco: input.endereco || null,
          observacoes: input.observacoes || null,
          statusAtivo: input.statusAtivo,
        },
        include: clienteInclude,
      });

      await tx.interacao.create({
        data: {
          clienteId: novoCliente.id,
          canal: "SISTEMA",
          resultado: "REGISTRO_INTERNO",
          responsavelId: context.usuarioId,
          responsavelNome: context.usuarioNome,
          observacoes: "Cliente cadastrado no sistema.",
        },
      });

      await registrarAuditoria(tx, context, {
        entidade: "Cliente",
        entidadeId: novoCliente.id,
        acao: "CRIAR",
        depois: toAuditJson(novoCliente),
      });

      return novoCliente;
    });

    return created(serializeCliente(cliente));
  } catch (error) {
    if (error instanceof Prisma.PrismaClientKnownRequestError && error.code === "P2002") {
      return fail("Já existe um cliente com este documento.", 409);
    }

    return handleRouteError(error);
  }
}
