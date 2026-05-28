import { StatusBadge } from "@/components/status-badge";
import { Card, CardContent } from "@/components/ui/card";
import { statusTarefaLabels, tipoTarefaLabels } from "@/lib/constants";
import { formatDate } from "@/lib/formatters";
import { listarTarefasPage } from "@/lib/server/queries";

export const dynamic = "force-dynamic";

export default async function AgendaPage() {
  const tarefas = await listarTarefasPage();

  return (
    <div className="space-y-6">
      <header>
        <h1 className="text-2xl font-semibold tracking-tight text-slate-950">Agenda de próximas ações</h1>
        <p className="text-sm text-slate-500">{tarefas.length} registros</p>
      </header>

      <Card>
        <CardContent className="grid gap-3">
          {tarefas.map((tarefa) => (
            <article key={tarefa.id} className="rounded-md border border-slate-200 p-4">
              <div className="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div>
                  <p className="font-medium text-slate-950">{tipoTarefaLabels[tarefa.tipo]}</p>
                  <p className="mt-1 text-sm text-slate-600">{tarefa.descricao}</p>
                  <p className="mt-2 text-sm text-slate-500">{tarefa.cliente.nome}</p>
                </div>
                <div className="flex shrink-0 flex-col items-start gap-2 sm:items-end">
                  <StatusBadge status={tarefa.status} label={statusTarefaLabels[tarefa.status]} />
                  <span className="text-sm text-slate-500">{formatDate(tarefa.dataAgendada)}</span>
                  <span className="text-xs text-slate-500">{tarefa.responsavelNome}</span>
                </div>
              </div>
            </article>
          ))}
        </CardContent>
      </Card>
    </div>
  );
}
