import { Plus, Search } from "lucide-react";
import Link from "next/link";
import { StatusBadge } from "@/components/status-badge";
import { Button } from "@/components/ui/button";
import { Card, CardContent } from "@/components/ui/card";
import { statusCobrancaLabels, tipoClienteLabels } from "@/lib/constants";
import { formatCurrency, formatDate } from "@/lib/formatters";
import { listarClientesPage } from "@/lib/server/queries";

export const dynamic = "force-dynamic";

export default async function ClientesPage() {
  const clientes = await listarClientesPage();

  return (
    <div className="space-y-6">
      <header className="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <h1 className="text-2xl font-semibold tracking-tight text-slate-950">Clientes</h1>
          <p className="text-sm text-slate-500">{clientes.length} registros</p>
        </div>
        <Link href="/clientes/novo">
          <Button>
            <Plus className="h-4 w-4" aria-hidden="true" />
            Novo cliente
          </Button>
        </Link>
      </header>

      <Card>
        <CardContent className="p-0">
          <div className="flex items-center gap-2 border-b border-slate-200 px-4 py-3 text-sm text-slate-500">
            <Search className="h-4 w-4" aria-hidden="true" />
            Nome, CPF/CNPJ, e-mail ou telefone
          </div>
          <div className="overflow-x-auto">
            <table className="min-w-full divide-y divide-slate-200 text-left text-sm">
              <thead className="bg-slate-50 text-xs uppercase text-slate-500">
                <tr>
                  <th className="px-4 py-3">Cliente</th>
                  <th className="px-4 py-3">Tipo</th>
                  <th className="px-4 py-3">Contato</th>
                  <th className="px-4 py-3">Cobrança</th>
                  <th className="px-4 py-3">Próxima ação</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-slate-200 bg-white">
                {clientes.map((cliente) => {
                  const cobranca = cliente.cobrancas[0];
                  const total = cliente.cobrancas.reduce((sum, item) => sum + Number(item.valorTotal), 0);

                  return (
                    <tr key={cliente.id} className="hover:bg-slate-50">
                      <td className="px-4 py-3">
                        <Link href={`/clientes/${cliente.id}`} className="font-medium text-slate-950 hover:underline">
                          {cliente.nome}
                        </Link>
                        <p className="mt-1 text-xs text-slate-500">{cliente.documento}</p>
                      </td>
                      <td className="px-4 py-3 text-slate-600">{tipoClienteLabels[cliente.tipoCliente]}</td>
                      <td className="px-4 py-3 text-slate-600">
                        <p>{cliente.email}</p>
                        <p className="text-xs text-slate-500">{cliente.telefone}</p>
                      </td>
                      <td className="px-4 py-3">
                        <div className="flex flex-col gap-2">
                          {cobranca ? (
                            <StatusBadge status={cobranca.statusCobranca} label={statusCobrancaLabels[cobranca.statusCobranca]} />
                          ) : (
                            <StatusBadge status="RASCUNHO" label="Sem cobrança" />
                          )}
                          <span className="text-sm text-slate-600">{formatCurrency(total)}</span>
                        </div>
                      </td>
                      <td className="px-4 py-3 text-slate-600">
                        {cliente.tarefas[0] ? (
                          <>
                            <p>{cliente.tarefas[0].descricao}</p>
                            <p className="text-xs text-slate-500">{formatDate(cliente.tarefas[0].dataAgendada)}</p>
                          </>
                        ) : (
                          "-"
                        )}
                      </td>
                    </tr>
                  );
                })}
              </tbody>
            </table>
          </div>
        </CardContent>
      </Card>
    </div>
  );
}
