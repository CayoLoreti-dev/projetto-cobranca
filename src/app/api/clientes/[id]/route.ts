import { Prisma } from "@/generated/prisma/client";
import { clienteUpdateSchema } from "@/lib/validations";
import { fail, handleRouteError, ok } from "@/lib/server/api-response";
import { registrarAuditoria, toAuditJson } from "@/lib/server/audit";
import { getRequestContext } from "@/lib/server/context";
import { prisma } from "@/lib/server/prisma";
import { readJson } from "@/lib/server/request";
import { clienteInclude, serializeCliente } from "@/lib/server/serializers";

export const runtime = "nodejs";
export const dynamic = "force-dynamic";

type RouteContext = { params: Promise<{ id: string }> | { id: string } };

export async function GET(_request: Request, { params }: RouteContext) {
  try {
    const { id } = await params;
    const cliente = await prisma.cliente.findUnique({
      where: { id },
      include: clienteInclude,
    });

    if (!cliente) {
      return fail("Cliente não encontrado.", 404);
    }

    return ok(serializeCliente(cliente));
  } catch (error) {
    return handleRouteError(error);
  }
}

export async function PATCH(request: Request, { params }: RouteContext) {
  try {
    const { id } = await params;
    const body = await readJson(request);
    const input = clienteUpdateSchema.parse(body);
    const context = await getRequestContext(request);

    const cliente = await prisma.$transaction(async (tx) => {
      const antes = await tx.cliente.findUnique({ where: { id }, include: clienteInclude });

      if (!antes) {
        return null;
      }

      const atualizado = await tx.cliente.update({
        where: { id },
        data: {
          ...(input.nome ? { nome: input.nome } : {}),
          ...(input.tipoCliente ? { tipoCliente: input.tipoCliente } : {}),
          ...(input.documento ? { documento: input.documento } : {}),
          ...(input.responsavelFinanceiro ? { responsavelFinanceiro: input.responsavelFinanceiro } : {}),
          ...(input.email ? { email: input.email } : {}),
          ...(input.telefone ? { telefone: input.telefone } : {}),
          ...(typeof input.statusAtivo === "boolean" ? { statusAtivo: input.statusAtivo } : {}),
          ...(input.whatsapp !== undefined ? { whatsapp: input.whatsapp || null } : {}),
          ...(input.endereco !== undefined ? { endereco: input.endereco || null } : {}),
          ...(input.observacoes !== undefined ? { observacoes: input.observacoes || null } : {}),
        },
        include: clienteInclude,
      });

      await registrarAuditoria(tx, context, {
        entidade: "Cliente",
        entidadeId: id,
        acao: "ATUALIZAR",
        antes: toAuditJson(antes),
        depois: toAuditJson(atualizado),
      });

      return atualizado;
    });

    if (!cliente) {
      return fail("Cliente não encontrado.", 404);
    }

    return ok(serializeCliente(cliente));
  } catch (error) {
    if (error instanceof Prisma.PrismaClientKnownRequestError && error.code === "P2002") {
      return fail("Já existe um cliente com este documento.", 409);
    }

    return handleRouteError(error);
  }
}

export async function DELETE(request: Request, { params }: RouteContext) {
  try {
    const { id } = await params;
    const context = await getRequestContext(request);

    const deleted = await prisma.$transaction(async (tx) => {
      const antes = await tx.cliente.findUnique({ where: { id }, include: clienteInclude });

      if (!antes) {
        return false;
      }

      await registrarAuditoria(tx, context, {
        entidade: "Cliente",
        entidadeId: id,
        acao: "EXCLUIR",
        antes: toAuditJson(antes),
      });

      await tx.cliente.delete({ where: { id } });
      return true;
    });

    if (!deleted) {
      return fail("Cliente não encontrado.", 404);
    }

    return ok({ deleted: true });
  } catch (error) {
    return handleRouteError(error);
  }
}
