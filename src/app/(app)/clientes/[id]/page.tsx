import { notFound } from "next/navigation";
import { StatusBadge } from "@/components/status-badge";
import { Card, CardContent, CardHeader } from "@/components/ui/card";
import {
  canalLabels,
  resultadoLabels,
  statusCobrancaLabels,
  statusParcelaLabels,
  statusTarefaLabels,
  tipoClienteLabels,
  tipoCobrancaLabels,
} from "@/lib/constants";
import { formatCurrency, formatDate, formatDateTime } from "@/lib/formatters";
import { buscarClientePage } from "@/lib/server/queries";

export const dynamic = "force-dynamic";

type PageProps = { params: Promise<{ id: string }> | { id: string } };

export default async function ClienteDetalhePage({ params }: PageProps) {
  const { id } = await params;
  const cliente = await buscarClientePage(id);

  if (!cliente) {
    notFound();
  }

  return (
    <div className="space-y-6">
      <header className="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
        <div>
          <h1 className="text-2xl font-semibold tracking-tight text-slate-950">{cliente.nome}</h1>
          <p className="text-sm text-slate-500">
            {tipoClienteLabels[cliente.tipoCliente]} · {cliente.documento}
          </p>
        </div>
        <StatusBadge status={cliente.statusAtivo ? "PAGA" : "CANCELADA"} label={cliente.statusAtivo ? "Ativo" : "Inativo"} />
      </header>

      <section className="grid gap-4 lg:grid-cols-4">
        <InfoCard label="Responsável" value={cliente.responsavelFinanceiro} />
        <InfoCard label="E-mail" value={cliente.email} />
        <InfoCard label="Telefone" value={cliente.telefone} />
        <InfoCard label="WhatsApp" value={cliente.whatsapp ?? "-"} />
      </section>

      <section className="grid gap-6 xl:grid-cols-[1.15fr_0.85fr]">
        <Card>
          <CardHeader>
            <h2 className="text-base font-semibold text-slate-950">Cobranças</h2>
          </CardHeader>
          <CardContent className="space-y-4">
            {cliente.cobrancas.map((cobranca) => (
              <article key={cobranca.id} className="rounded-md border border-slate-200 p-4">
                <div className="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                  <div>
                    <p className="font-medium text-slate-950">{tipoCobrancaLabels[cobranca.tipoCobranca]}</p>
                    <p className="mt-1 text-sm text-slate-600">{formatCurrency(cobranca.valorTotal)}</p>
                  </div>
                  <div className="flex shrink-0 flex-col items-start gap-2 sm:items-end">
                    <StatusBadge status={cobranca.statusCobranca} label={statusCobrancaLabels[cobranca.statusCobranca]} />
                    <span className="text-sm text-slate-500">{formatDate(cobranca.dataVencimentoPrincipal)}</span>
                  </div>
                </div>

                <div className="mt-4 overflow-x-auto">
                  <table className="min-w-full divide-y divide-slate-200 text-left text-sm">
                    <thead className="bg-slate-50 text-xs uppercase text-slate-500">
                      <tr>
                        <th className="px-3 py-2">Parcela</th>
                        <th className="px-3 py-2">Valor</th>
                        <th className="px-3 py-2">Vencimento</th>
                        <th className="px-3 py-2">Status</th>
                      </tr>
                    </thead>
                    <tbody className="divide-y divide-slate-200">
                      {cobranca.parcelas.map((parcela) => (
                        <tr key={parcela.id}>
                          <td className="px-3 py-2 font-medium text-slate-950">#{parcela.numeroParcela}</td>
                          <td className="px-3 py-2 text-slate-600">{formatCurrency(parcela.valor)}</td>
                          <td className="px-3 py-2 text-slate-600">{formatDate(parcela.vencimento)}</td>
                          <td className="px-3 py-2">
                            <StatusBadge status={parcela.status} label={statusParcelaLabels[parcela.status]} />
                          </td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </div>
              </article>
            ))}
          </CardContent>
        </Card>

        <div className="space-y-6">
          <Card>
            <CardHeader>
              <h2 className="text-base font-semibold text-slate-950">Agenda</h2>
            </CardHeader>
            <CardContent className="space-y-3">
              {cliente.tarefas.map((tarefa) => (
                <div key={tarefa.id} className="rounded-md border border-slate-200 p-3">
                  <div className="flex items-start justify-between gap-3">
                    <p className="text-sm font-medium text-slate-950">{tarefa.descricao}</p>
                    <StatusBadge status={tarefa.status} label={statusTarefaLabels[tarefa.status]} />
                  </div>
                  <p className="mt-2 text-sm text-slate-500">{formatDate(tarefa.dataAgendada)}</p>
                </div>
              ))}
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <h2 className="text-base font-semibold text-slate-950">Interações</h2>
            </CardHeader>
            <CardContent className="space-y-3">
              {cliente.interacoes.map((interacao) => (
                <div key={interacao.id} className="rounded-md border border-slate-200 p-3">
                  <div className="flex flex-wrap items-center justify-between gap-2">
                    <p className="text-sm font-medium text-slate-950">
                      {canalLabels[interacao.canal]} · {resultadoLabels[interacao.resultado]}
                    </p>
                    <p className="text-xs text-slate-500">{formatDateTime(interacao.dataHora)}</p>
                  </div>
                  <p className="mt-2 text-sm text-slate-600">{interacao.observacoes}</p>
                  <p className="mt-2 text-xs text-slate-500">{interacao.responsavelNome}</p>
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
