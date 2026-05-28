import { Card, CardContent } from "@/components/ui/card";
import { formatDateTime } from "@/lib/formatters";
import { listarAuditoriaPage } from "@/lib/server/queries";

export const dynamic = "force-dynamic";

export default async function AuditoriaPage() {
  const auditorias = await listarAuditoriaPage();

  return (
    <div className="space-y-6">
      <header>
        <h1 className="text-2xl font-semibold tracking-tight text-slate-950">Auditoria</h1>
        <p className="text-sm text-slate-500">{auditorias.length} registros</p>
      </header>

      <Card>
        <CardContent className="overflow-x-auto p-0">
          <table className="min-w-full divide-y divide-slate-200 text-left text-sm">
            <thead className="bg-slate-50 text-xs uppercase text-slate-500">
              <tr>
                <th className="px-4 py-3">Data</th>
                <th className="px-4 py-3">Entidade</th>
                <th className="px-4 py-3">Ação</th>
                <th className="px-4 py-3">Usuário</th>
                <th className="px-4 py-3">Origem</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-200 bg-white">
              {auditorias.map((auditoria) => (
                <tr key={auditoria.id} className="hover:bg-slate-50">
                  <td className="px-4 py-3 text-slate-600">{formatDateTime(auditoria.dataHora)}</td>
                  <td className="px-4 py-3">
                    <p className="font-medium text-slate-950">{auditoria.entidade}</p>
                    <p className="text-xs text-slate-500">{auditoria.entidadeId}</p>
                  </td>
                  <td className="px-4 py-3 text-slate-600">{auditoria.acao}</td>
                  <td className="px-4 py-3 text-slate-600">{auditoria.usuarioNome}</td>
                  <td className="px-4 py-3 text-slate-600">{auditoria.origem}</td>
                </tr>
              ))}
            </tbody>
          </table>
        </CardContent>
      </Card>
    </div>
  );
}
