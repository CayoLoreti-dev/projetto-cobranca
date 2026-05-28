import { Prisma } from "@/generated/prisma/client";
import { hash } from "bcryptjs";
import { usuarioCreateSchema, paginationSchema, perfilUsuarioValues } from "@/lib/validations";
import { created, fail, handleRouteError, ok, parseSearchParams } from "@/lib/server/api-response";
import { registrarAuditoria, toAuditJson } from "@/lib/server/audit";
import { getRequestContext } from "@/lib/server/context";
import { prisma } from "@/lib/server/prisma";
import { readJson } from "@/lib/server/request";
import { omitSenhaHash } from "@/lib/server/safe-user";

export const runtime = "nodejs";
export const dynamic = "force-dynamic";

export async function GET(request: Request) {
  try {
    const query = paginationSchema
      .extend({
        perfil: paginationSchema.shape.busca.optional(),
        ativo: paginationSchema.shape.busca.optional(),
      })
      .parse(parseSearchParams(request));
    const perfil = perfilUsuarioValues.find((value) => value === query.perfil);
    const ativo = query.ativo === "true" ? true : query.ativo === "false" ? false : undefined;

    const where: Prisma.UsuarioWhereInput = {
      ...(perfil ? { perfil } : {}),
      ...(typeof ativo === "boolean" ? { ativo } : {}),
      ...(query.busca
        ? {
            OR: [
              { nome: { contains: query.busca, mode: "insensitive" } },
              { email: { contains: query.busca, mode: "insensitive" } },
            ],
          }
        : {}),
    };

    const [usuarios, total] = await prisma.$transaction([
      prisma.usuario.findMany({
        where,
        orderBy: { nome: "asc" },
        skip: (query.page - 1) * query.pageSize,
        take: query.pageSize,
        include: {
          tarefas: { where: { status: { in: ["ABERTA", "EM_ANDAMENTO", "ATRASADA"] } } },
          cobrancasResponsaveis: { where: { statusCobranca: { notIn: ["PAGA", "CANCELADA"] } } },
        },
      }),
      prisma.usuario.count({ where }),
    ]);

    return ok({
      items: usuarios.map(omitSenhaHash),
      pagination: { page: query.page, pageSize: query.pageSize, total },
    });
  } catch (error) {
    return handleRouteError(error);
  }
}

export async function POST(request: Request) {
  try {
    const body = await readJson(request);
    const input = usuarioCreateSchema.parse(body);
    const context = await getRequestContext(request);

    const usuario = await prisma.$transaction(async (tx) => {
      const senhaHash = await hash(input.senha, 10);
      const novoUsuario = await tx.usuario.create({
        data: {
          nome: input.nome,
          email: input.email,
          senhaHash,
          perfil: input.perfil,
          permissoes: input.permissoes,
          ativo: input.ativo,
        },
      });

      await registrarAuditoria(tx, context, {
        entidade: "Usuario",
        entidadeId: novoUsuario.id,
        acao: "CRIAR",
        depois: toAuditJson({ ...novoUsuario, senhaHash: "[protegido]" }),
      });

      return novoUsuario;
    });

    return created(omitSenhaHash(usuario));
  } catch (error) {
    if (error instanceof Prisma.PrismaClientKnownRequestError && error.code === "P2002") {
      return fail("Já existe um usuário com este e-mail.", 409);
    }

    return handleRouteError(error);
  }
}
