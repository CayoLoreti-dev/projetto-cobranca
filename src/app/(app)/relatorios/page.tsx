import { AlertTriangle, CheckCircle2, Clock, WalletCards } from "lucide-react";
import { MetricCard } from "@/components/metric-card";
import { Card, CardContent, CardHeader } from "@/components/ui/card";
import { formatCurrency } from "@/lib/formatters";
import { getDashboardData, listarParcelasPage } from "@/lib/server/queries";

export const dynamic = "force-dynamic";

export default async function RelatoriosPage() {
  const [dashboard, parcelas] = await Promise.all([getDashboardData(), listarParcelasPage()]);
  const pagas = parcelas.filter((parcela) => parcela.status === "PAGA");
  const pendentes = parcelas.filter((parcela) => ["PENDENTE", "ENVIADA"].includes(parcela.status));
  const atraso = parcelas.filter((parcela) => ["ATRASADA", "EM_NEGATIVACAO"].includes(parcela.status));

  return (
    <div className="space-y-6">
      <header>
        <h1 className="text-2xl font-semibold tracking-tight text-slate-950">Relatórios</h1>
        <p className="text-sm text-slate-500">Carteira e inadimplência</p>
      </header>

      <section className="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <MetricCard label="Valor monitorado" value={formatCurrency(dashboard.metricas.valorCarteira)} icon={WalletCards} />
        <MetricCard label="Parcelas pagas" value={String(pagas.length)} icon={CheckCircle2} />
        <MetricCard label="Parcelas pendentes" value={String(pendentes.length)} icon={Clock} />
        <MetricCard label="Parcelas críticas" value={String(atraso.length)} icon={AlertTriangle} />
      </section>

      <Card>
        <CardHeader>
          <h2 className="text-base font-semibold text-slate-950">Resumo operacional</h2>
        </CardHeader>
        <CardContent>
          <div className="grid gap-4 md:grid-cols-3">
            <ReportLine label="Clientes ativos" value={String(dashboard.metricas.clientesAtivos)} />
            <ReportLine label="Cobranças abertas" value={String(dashboard.metricas.totalCobrancas)} />
            <ReportLine label="Tarefas pendentes" value={String(dashboard.metricas.tarefasPendentes)} />
          </div>
        </CardContent>
      </Card>
    </div>
  );
}

function ReportLine({ label, value }: { label: string; value: string }) {
  return (
    <div className="rounded-md border border-slate-200 p-4">
      <p className="text-sm text-slate-500">{label}</p>
      <p className="mt-2 text-xl font-semibold text-slate-950">{value}</p>
    </div>
  );
}
