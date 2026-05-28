import { BoletosSection } from "@/components/boletos/boletos-section";
import { listarBoletosPage } from "@/lib/server/queries";

export const dynamic = "force-dynamic";

export default async function BoletosPage() {
  const boletos = await listarBoletosPage();

  return (
    <BoletosSection
      title="Boletos"
      description="Todos os tipos de cliente"
      activeKey="todos"
      boletos={boletos}
    />
  );
}
