import { fail, handleRouteError, ok } from "@/lib/server/api-response";
import { prisma } from "@/lib/server/prisma";

export const runtime = "nodejs";
export const dynamic = "force-dynamic";

type RouteContext = { params: Promise<{ id: string }> | { id: string } };

export async function GET(_request: Request, { params }: RouteContext) {
  try {
    const { id } = await params;
    const cliente = await prisma.cliente.findUnique({
      where: { id },
      select: {
        id: true,
        cobrancas: {
          select: {
            id: true,
            parcelas: { orderBy: { numeroParcela: "asc" }, include: { boleto: true } },
          },
        },
      },
    });

    if (!cliente) {
      return fail("Cliente não encontrado.", 404);
    }

    return ok(cliente.cobrancas.flatMap((cobranca) => cobranca.parcelas));
  } catch (error) {
    return handleRouteError(error);
  }
}
