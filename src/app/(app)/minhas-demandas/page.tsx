import { getServerSession } from "next-auth";
import Link from "next/link";
import { StatusBadge } from "@/components/status-badge";
import { Card, CardContent } from "@/components/ui/card";
import { authOptions } from "@/lib/auth";
import { statusTarefaLabels, tipoTarefaLabels } from "@/lib/constants";
import { formatDate } from "@/lib/formatters";
import { listarMinhasDemandasPage } from "@/lib/server/queries";

export const dynamic = "force-dynamic";

export default async function MinhasDemandasPage() {
  const session = await getServerSession(authOptions);
  const tarefas = await listarMinhasDemandasPage(session?.user?.id);

  return (
    <div className="space-y-6">
      <header>
        <h1 className="text-2xl font-semibold tracking-tight text-slate-950">Minhas demandas</h1>
        <p className="text-sm text-slate-500">
          {session?.user?.name ? `Responsável: ${session.user.name}` : "Demandas abertas do setor"}
        </p>
      </header>

      <section className="grid gap-4 md:grid-cols-3">
        <ResumoCard label="Abertas" value={String(tarefas.filter((tarefa) => tarefa.status === "ABERTA").length)} />
        <ResumoCard label="Em andamento" value={String(tarefas.filter((tarefa) => tarefa.status === "EM_ANDAMENTO").length)} />
        <ResumoCard label="Atrasadas" value={String(tarefas.filter((tarefa) => tarefa.status === "ATRASADA").length)} />
      </section>

      <Card>
        <CardContent className="space-y-3">
          {tarefas.map((tarefa) => (
            <article key={tarefa.id} className="rounded-md border border-slate-200 p-4">
              <div className="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div>
                  <p className="font-medium text-slate-950">{tipoTarefaLabels[tarefa.tipo]}</p>
                  <p className="mt-1 text-sm text-slate-600">{tarefa.descricao}</p>
                  <Link href={`/clientes/${tarefa.clienteId}`} className="mt-2 inline-block text-sm font-medium text-sky-700 hover:underline">
                    {tarefa.cliente.nome}
                  </Link>
                </div>
                <div className="flex shrink-0 flex-col items-start gap-2 sm:items-end">
                  <StatusBadge status={tarefa.status} label={statusTarefaLabels[tarefa.status]} />
                  <span className="text-sm text-slate-500">{formatDate(tarefa.dataAgendada)}</span>
                  <span className="text-xs text-slate-500">{tarefa.responsavelNome}</span>
                </div>
              </div>
            </article>
          ))}

          {tarefas.length === 0 ? (
            <div className="px-4 py-8 text-center text-sm text-slate-500">Nenhuma demanda aberta para este responsável.</div>
          ) : null}
        </CardContent>
      </Card>
    </div>
  );
}

function ResumoCard({ label, value }: { label: string; value: string }) {
  return (
    <article className="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
      <p className="text-sm text-slate-500">{label}</p>
      <p className="mt-2 text-2xl font-semibold text-slate-950">{value}</p>
    </article>
  );
}
