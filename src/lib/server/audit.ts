import type { AcaoAuditoria, OrigemAuditoria, Prisma } from "@/generated/prisma/client";
import type { RequestContext } from "@/lib/server/context";

interface AuditInput {
  entidade: string;
  entidadeId: string;
  acao: AcaoAuditoria;
  antes?: Prisma.InputJsonValue;
  depois?: Prisma.InputJsonValue;
  origem?: OrigemAuditoria;
}

export async function registrarAuditoria(
  tx: Prisma.TransactionClient,
  context: RequestContext,
  input: AuditInput,
) {
  return tx.auditoria.create({
    data: {
      entidade: input.entidade,
      entidadeId: input.entidadeId,
      acao: input.acao,
      antes: input.antes,
      depois: input.depois,
      usuarioId: context.usuarioId,
      usuarioNome: context.usuarioNome,
      ip: context.ip,
      origem: input.origem ?? "API",
    },
  });
}

export function toAuditJson(value: unknown): Prisma.InputJsonValue {
  return JSON.parse(JSON.stringify(value)) as Prisma.InputJsonValue;
}
