import { ok, handleRouteError } from "@/lib/server/api-response";
import { getDashboardData } from "@/lib/server/queries";

export const runtime = "nodejs";
export const dynamic = "force-dynamic";

export async function GET() {
  try {
    return ok(await getDashboardData());
  } catch (error) {
    return handleRouteError(error);
  }
}
