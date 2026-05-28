import { notFound } from "next/navigation";
import { StatusBadge } from "@/components/status-badge";
import { Card, CardContent, CardHeader } from "@/components/ui/card";
import {
  canalLabels,
  resultadoLabels,
  statusBoletoLabels,
  statusCobrancaLabels,
  statusParcelaLabels,
  statusTarefaLabels,
  tipoCobrancaLabels,
  tipoTarefaLabels,
} from "@/lib/constants";
import { formatCurrency, formatDate, formatDateTime } from "@/lib/formatters";
import { buscarCobrancaPage } from "@/lib/server/queries";

export const dynamic = "force-dynamic";

type PageProps = { params: Promise<{ id: string }> | { id: string } };

export default async function CobrancaDetalhePage({ params }: PageProps) {
  const { id } = await params;
  const cobranca = await buscarCobrancaPage(id);

  if (!cobranca) {
    notFound();
  }

  return (
    <div className="space-y-6">
      <header className="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div>
          <h1 className="text-2xl font-semibold tracking-tight text-slate-950">{cobranca.cliente.nome}</h1>
          <p className="text-sm text-slate-500">
            {tipoCobrancaLabels[cobranca.tipoCobranca]} · {formatCurrency(cobranca.valorTotal)}
          </p>
        </div>
        <StatusBadge status={cobranca.statusCobranca} label={statusCobrancaLabels[cobranca.statusCobranca]} />
      </header>

      <section className="grid gap-4 md:grid-cols-3">
        <InfoCard label="Emissão" value={formatDate(cobranca.dataEmissao)} />
        <InfoCard label="Vencimento principal" value={formatDate(cobranca.dataVencimentoPrincipal)} />
        <InfoCard label="Próxima ação" value={cobranca.proximaAcao ?? "-"} />
      </section>

      <section className="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
        <Card>
          <CardHeader>
            <h2 className="text-base font-semibold text-slate-950">Parcelas</h2>
          </CardHeader>
          <CardContent className="overflow-x-auto p-0">
            <table className="min-w-full divide-y divide-slate-200 text-left text-sm">
              <thead className="bg-slate-50 text-xs uppercase text-slate-500">
                <tr>
                  <th className="px-4 py-3">Parcela</th>
                  <th className="px-4 py-3">Valor</th>
                  <th className="px-4 py-3">Vencimento</th>
                  <th className="px-4 py-3">Status</th>
                  <th className="px-4 py-3">Boleto</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-slate-200 bg-white">
                {cobranca.parcelas.map((parcela) => (
                  <tr key={parcela.id}>
                    <td className="px-4 py-3 font-medium text-slate-950">#{parcela.numeroParcela}</td>
                    <td className="px-4 py-3 text-slate-600">{formatCurrency(parcela.valor)}</td>
                    <td className="px-4 py-3 text-slate-600">{formatDate(parcela.vencimento)}</td>
                    <td className="px-4 py-3">
                      <StatusBadge status={parcela.status} label={statusParcelaLabels[parcela.status]} />
                    </td>
                    <td className="px-4 py-3">
                      {parcela.boleto ? (
                        <StatusBadge status={parcela.boleto.status} label={statusBoletoLabels[parcela.boleto.status]} />
                      ) : (
                        <span className="text-slate-500">-</span>
                      )}
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </CardContent>
        </Card>

        <div className="space-y-6">
          <Card>
            <CardHeader>
              <h2 className="text-base font-semibold text-slate-950">Agenda</h2>
            </CardHeader>
            <CardContent className="space-y-3">
              {cobranca.tarefas.map((tarefa) => (
                <div key={tarefa.id} className="rounded-md border border-slate-200 p-3">
                  <div className="flex items-start justify-between gap-3">
                    <div>
                      <p className="text-sm font-medium text-slate-950">{tipoTarefaLabels[tarefa.tipo]}</p>
                      <p className="mt-1 text-sm text-slate-600">{tarefa.descricao}</p>
                    </div>
                    <StatusBadge status={tarefa.status} label={statusTarefaLabels[tarefa.status]} />
                  </div>
                  <p className="mt-2 text-sm text-slate-500">{formatDate(tarefa.dataAgendada)}</p>
                </div>
              ))}
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <h2 className="text-base font-semibold text-slate-950">Histórico</h2>
            </CardHeader>
            <CardContent className="space-y-3">
              {cobranca.interacoes.map((interacao) => (
                <div key={interacao.id} className="rounded-md border border-slate-200 p-3">
                  <div className="flex flex-wrap items-center justify-between gap-2">
                    <p className="text-sm font-medium text-slate-950">
                      {canalLabels[interacao.canal]} · {resultadoLabels[interacao.resultado]}
                    </p>
                    <p className="text-xs text-slate-500">{formatDateTime(interacao.dataHora)}</p>
                  </div>
                  <p className="mt-2 text-sm text-slate-600">{interacao.observacoes}</p>
                </div>
              ))}
            </CardContent>
          </Card>
        </div>
      </section>
    </div>
  );
}

function InfoCard({ label, value }: { label: string; value: string }) {
  return (
    <article className="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
      <p className="text-sm text-slate-500">{label}</p>
      <p className="mt-2 text-sm font-medium text-slate-950">{value}</p>
    </article>
  );
}
