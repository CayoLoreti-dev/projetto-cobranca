import { StatusBadge } from "@/components/status-badge";
import { Card, CardContent } from "@/components/ui/card";
import { canalLabels, resultadoLabels } from "@/lib/constants";
import { formatDateTime } from "@/lib/formatters";
import { listarInteracoesPage } from "@/lib/server/queries";

export const dynamic = "force-dynamic";

export default async function InteracoesPage() {
  const interacoes = await listarInteracoesPage();

  return (
    <div className="space-y-6">
      <header>
        <h1 className="text-2xl font-semibold tracking-tight text-slate-950">Histórico de interações</h1>
        <p className="text-sm text-slate-500">{interacoes.length} registros</p>
      </header>

      <Card>
        <CardContent className="space-y-3">
          {interacoes.map((interacao) => (
            <article key={interacao.id} className="rounded-md border border-slate-200 p-4">
              <div className="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div>
                  <p className="font-medium text-slate-950">{interacao.cliente.nome}</p>
                  <p className="mt-1 text-sm text-slate-600">{interacao.observacoes}</p>
                  <p className="mt-2 text-xs text-slate-500">{interacao.responsavelNome}</p>
                </div>
                <div className="flex shrink-0 flex-col items-start gap-2 sm:items-end">
                  <StatusBadge status="ENVIADA" label={canalLabels[interacao.canal]} />
                  <span className="text-sm text-slate-500">{resultadoLabels[interacao.resultado]}</span>
                  <span className="text-xs text-slate-500">{formatDateTime(interacao.dataHora)}</span>
                </div>
              </div>
            </article>
          ))}
        </CardContent>
      </Card>
    </div>
  );
}
