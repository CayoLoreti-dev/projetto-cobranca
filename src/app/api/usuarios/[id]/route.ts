import { Prisma } from "@/generated/prisma/client";
import { hash } from "bcryptjs";
import { usuarioUpdateSchema } from "@/lib/validations";
import { fail, handleRouteError, ok } from "@/lib/server/api-response";
import { registrarAuditoria, toAuditJson } from "@/lib/server/audit";
import { getRequestContext } from "@/lib/server/context";
import { prisma } from "@/lib/server/prisma";
import { readJson } from "@/lib/server/request";
import { omitSenhaHash } from "@/lib/server/safe-user";

export const runtime = "nodejs";
export const dynamic = "force-dynamic";

type RouteContext = { params: Promise<{ id: string }> | { id: string } };

export async function GET(_request: Request, { params }: RouteContext) {
  try {
    const { id } = await params;
    const usuario = await prisma.usuario.findUnique({
      where: { id },
      include: {
        tarefas: { orderBy: { dataAgendada: "asc" }, include: { cliente: true, cobranca: true } },
        cobrancasResponsaveis: { orderBy: { atualizadoEm: "desc" }, include: { cliente: true } },
      },
    });

    if (!usuario) {
      return fail("Usuário não encontrado.", 404);
    }

    return ok(omitSenhaHash(usuario));
  } catch (error) {
    return handleRouteError(error);
  }
}

export async function PATCH(request: Request, { params }: RouteContext) {
  try {
    const { id } = await params;
    const body = await readJson(request);
    const input = usuarioUpdateSchema.parse(body);
    const context = await getRequestContext(request);

    const usuario = await prisma.$transaction(async (tx) => {
      const antes = await tx.usuario.findUnique({ where: { id } });

      if (!antes) {
        return null;
      }

      const senhaHash = input.senha ? await hash(input.senha, 10) : undefined;
      const atualizado = await tx.usuario.update({
        where: { id },
        data: {
          ...(input.nome ? { nome: input.nome } : {}),
          ...(input.email ? { email: input.email } : {}),
          ...(senhaHash ? { senhaHash } : {}),
          ...(input.perfil ? { perfil: input.perfil } : {}),
          ...(input.permissoes ? { permissoes: input.permissoes } : {}),
          ...(typeof input.ativo === "boolean" ? { ativo: input.ativo } : {}),
        },
      });

      await registrarAuditoria(tx, context, {
        entidade: "Usuario",
        entidadeId: id,
        acao: "ATUALIZAR",
        antes: toAuditJson({ ...antes, senhaHash: "[protegido]" }),
        depois: toAuditJson({ ...atualizado, senhaHash: "[protegido]" }),
      });

      return atualizado;
    });

    if (!usuario) {
      return fail("Usuário não encontrado.", 404);
    }

    return ok(omitSenhaHash(usuario));
  } catch (error) {
    if (error instanceof Prisma.PrismaClientKnownRequestError && error.code === "P2002") {
      return fail("Já existe um usuário com este e-mail.", 409);
    }

    return handleRouteError(error);
  }
}
