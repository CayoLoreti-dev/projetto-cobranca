import { CobrancaForm } from "@/components/forms/cobranca-form";
import { Card, CardContent, CardHeader } from "@/components/ui/card";
import { listarClientesOptions } from "@/lib/server/queries";

export const dynamic = "force-dynamic";

export default async function NovaCobrancaPage() {
  const clientes = await listarClientesOptions();

  return (
    <div className="space-y-6">
      <header>
        <h1 className="text-2xl font-semibold tracking-tight text-slate-950">Nova cobrança</h1>
        <p className="text-sm text-slate-500">À vista ou parcelada</p>
      </header>

      <Card>
        <CardHeader>
          <h2 className="text-base font-semibold text-slate-950">Dados da cobrança</h2>
        </CardHeader>
        <CardContent>
          <CobrancaForm clientes={clientes} />
        </CardContent>
      </Card>
    </div>
  );
}
