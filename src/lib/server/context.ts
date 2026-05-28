import { getServerSession } from "next-auth";
import { authOptions } from "@/lib/auth";

export interface RequestContext {
  usuarioId?: string;
  usuarioNome: string;
  ip?: string;
}

export async function getRequestContext(request?: Request): Promise<RequestContext> {
  const session = await getServerSession(authOptions).catch(() => null);
  const forwardedFor = request?.headers.get("x-forwarded-for");

  return {
    usuarioId: session?.user?.id,
    usuarioNome: session?.user?.name ?? "Sistema",
    ip: forwardedFor?.split(",")[0]?.trim(),
  };
}
