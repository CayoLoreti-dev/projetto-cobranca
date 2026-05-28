import { AlertTriangle, ClipboardList, UsersRound, WalletCards } from "lucide-react";
import Link from "next/link";
import { MetricCard } from "@/components/metric-card";
import { StatusBadge } from "@/components/status-badge";
import { Card, CardContent, CardHeader } from "@/components/ui/card";
import { statusCobrancaLabels, statusTarefaLabels, tipoTarefaLabels } from "@/lib/constants";
import { formatCurrency, formatDate } from "@/lib/formatters";
import { getDashboardData } from "@/lib/server/queries";

export const dynamic = "force-dynamic";

export default async function DashboardPage() {
  const dashboard = await getDashboardData();

  return (
    <div className="space-y-6">
      <header className="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
          <p className="text-sm font-medium text-slate-500">AP Vistoria Predial</p>
          <h1 className="text-2xl font-semibold tracking-tight text-slate-950">Dashboard financeiro</h1>
        </div>
        {dashboard.usingDemoData ? (
          <span className="rounded-md border border-amber-200 bg-amber-50 px-3 py-2 text-sm font-medium text-amber-800">
            Dados demonstrativos
          </span>
        ) : null}
      </header>

      <section className="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <MetricCard label="Clientes ativos" value={String(dashboard.metricas.clientesAtivos)} detail={`${dashboard.metricas.totalClientes} cadastrados`} icon={UsersRound} />
        <MetricCard label="Cobranças abertas" value={String(dashboard.metricas.totalCobrancas)} detail="Carteira operacional" icon={WalletCards} />
        <MetricCard label="Parcelas em atraso" value={String(dashboard.metricas.parcelasAtrasadas)} detail="Atraso ou negativação" icon={AlertTriangle} />
        <MetricCard label="Valor monitorado" value={formatCurrency(dashboard.metricas.valorCarteira)} detail="Total emitido" icon={ClipboardList} />
      </section>

      <section className="grid gap-6 xl:grid-cols-[1fr_1fr]">
        <Card>
          <CardHeader>
            <h2 className="text-base font-semibold text-slate-950">Próximas ações</h2>
          </CardHeader>
          <CardContent className="space-y-3">
            {dashboard.proximasTarefas.map((tarefa) => (
              <div key={tarefa.id} className="flex flex-col gap-3 rounded-md border border-slate-200 p-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                  <p className="font-medium text-slate-950">{tipoTarefaLabels[tarefa.tipo]}</p>
                  <p className="mt-1 text-sm text-slate-600">{tarefa.descricao}</p>
                  <p className="mt-2 text-sm text-slate-500">{tarefa.cliente.nome}</p>
                </div>
                <div className="flex shrink-0 flex-col items-start gap-2 sm:items-end">
                  <StatusBadge status={tarefa.status} label={statusTarefaLabels[tarefa.status]} />
                  <span className="text-sm text-slate-500">{formatDate(tarefa.dataAgendada)}</span>
                </div>
              </div>
            ))}
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <h2 className="text-base font-semibold text-slate-950">Casos críticos</h2>
          </CardHeader>
          <CardContent className="space-y-3">
            {dashboard.cobrancasCriticas.map((cobranca) => (
              <Link
                key={cobranca.id}
                href={`/cobrancas/${cobranca.id}`}
                className="block rounded-md border border-slate-200 p-4 transition hover:border-slate-300 hover:bg-slate-50"
              >
                <div className="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                  <div>
                    <p className="font-medium text-slate-950">{cobranca.cliente.nome}</p>
                    <p className="mt-1 text-sm text-slate-600">{formatCurrency(cobranca.valorTotal)}</p>
                  </div>
                  <div className="flex shrink-0 flex-col items-start gap-2 sm:items-end">
                    <StatusBadge status={cobranca.statusCobranca} label={statusCobrancaLabels[cobranca.statusCobranca]} />
                    <span className="text-sm text-slate-500">{formatDate(cobranca.dataVencimentoPrincipal)}</span>
                  </div>
                </div>
              </Link>
            ))}
          </CardContent>
        </Card>
      </section>
    </div>
  );
}
