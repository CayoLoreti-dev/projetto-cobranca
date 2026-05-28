import { StatusBadge } from "@/components/status-badge";
import { Card, CardContent } from "@/components/ui/card";
import { statusParcelaLabels } from "@/lib/constants";
import { formatCurrency, formatDate } from "@/lib/formatters";
import { listarParcelasPage } from "@/lib/server/queries";

export const dynamic = "force-dynamic";

export default async function ParcelasPage() {
  const parcelas = await listarParcelasPage();

  return (
    <div className="space-y-6">
      <header>
        <h1 className="text-2xl font-semibold tracking-tight text-slate-950">Parcelas</h1>
        <p className="text-sm text-slate-500">{parcelas.length} registros</p>
      </header>

      <Card>
        <CardContent className="overflow-x-auto p-0">
          <table className="min-w-full divide-y divide-slate-200 text-left text-sm">
            <thead className="bg-slate-50 text-xs uppercase text-slate-500">
              <tr>
                <th className="px-4 py-3">Cliente</th>
                <th className="px-4 py-3">Parcela</th>
                <th className="px-4 py-3">Valor</th>
                <th className="px-4 py-3">Vencimento</th>
                <th className="px-4 py-3">Status</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-200 bg-white">
              {parcelas.map((parcela) => (
                <tr key={parcela.id} className="hover:bg-slate-50">
                  <td className="px-4 py-3 font-medium text-slate-950">{parcela.cobranca.cliente.nome}</td>
                  <td className="px-4 py-3 text-slate-600">#{parcela.numeroParcela}</td>
                  <td className="px-4 py-3 text-slate-600">{formatCurrency(parcela.valor)}</td>
                  <td className="px-4 py-3 text-slate-600">{formatDate(parcela.vencimento)}</td>
                  <td className="px-4 py-3">
                    <StatusBadge status={parcela.status} label={statusParcelaLabels[parcela.status]} />
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </CardContent>
      </Card>
    </div>
  );
}
