import Link from "next/link";
import { StatusBadge } from "@/components/status-badge";
import { Card, CardContent } from "@/components/ui/card";
import { statusBoletoLabels, tipoClienteLabels } from "@/lib/constants";
import { formatCurrency, formatDate } from "@/lib/formatters";
import { cn } from "@/lib/utils";

const boletoTabs = [
  { href: "/boletos", label: "Todos", activeKey: "todos" },
  { href: "/boletos/pessoa-fisica", label: "Pessoa física", activeKey: "pessoa-fisica" },
  { href: "/boletos/pessoa-juridica", label: "Pessoa jurídica", activeKey: "pessoa-juridica" },
  { href: "/boletos/condominios", label: "Condomínios", activeKey: "condominios" },
];

type BoletoList = Array<{
  id: string;
  linhaDigitavel: string;
  valor: unknown;
  vencimento: Date | string;
  status: string;
  confirmacaoLeitura: boolean;
  confirmacaoRecebimento: boolean;
  parcela?: {
    numeroParcela?: number;
    cobranca: {
      cliente: {
        nome: string;
        tipoCliente: string;
      };
    };
  } | null;
  cobranca?: {
    cliente: {
      nome: string;
      tipoCliente: string;
    };
  } | null;
}>;

export function BoletosSection({
  title,
  description,
  activeKey,
  boletos,
}: {
  title: string;
  description: string;
  activeKey: string;
  boletos: BoletoList;
}) {
  return (
    <div className="space-y-6">
      <header className="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
        <div>
          <h1 className="text-2xl font-semibold tracking-tight text-slate-950">{title}</h1>
          <p className="text-sm text-slate-500">
            {description} · {boletos.length} registros
          </p>
        </div>
        <nav className="flex flex-wrap gap-2" aria-label="Filtros de boletos por tipo de cliente">
          {boletoTabs.map((tab) => (
            <Link
              key={tab.href}
              href={tab.href}
              className={cn(
                "rounded-md border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50",
                activeKey === tab.activeKey && "border-slate-950 bg-slate-950 text-white hover:bg-slate-900",
              )}
            >
              {tab.label}
            </Link>
          ))}
        </nav>
      </header>

      <Card>
        <CardContent className="overflow-x-auto p-0">
          <table className="min-w-full divide-y divide-slate-200 text-left text-sm">
            <thead className="bg-slate-50 text-xs uppercase text-slate-500">
              <tr>
                <th className="px-4 py-3">Cliente</th>
                <th className="px-4 py-3">Tipo</th>
                <th className="px-4 py-3">Parcela</th>
                <th className="px-4 py-3">Linha digitável</th>
                <th className="px-4 py-3">Valor</th>
                <th className="px-4 py-3">Vencimento</th>
                <th className="px-4 py-3">Status</th>
                <th className="px-4 py-3">Confirmações</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-200 bg-white">
              {boletos.map((boleto) => {
                const cliente = boleto.parcela?.cobranca.cliente ?? boleto.cobranca?.cliente;

                return (
                  <tr key={boleto.id} className="hover:bg-slate-50">
                    <td className="px-4 py-3 font-medium text-slate-950">{cliente?.nome ?? "-"}</td>
                    <td className="px-4 py-3 text-slate-600">{cliente ? tipoClienteLabels[cliente.tipoCliente] : "-"}</td>
                    <td className="px-4 py-3 text-slate-600">{boleto.parcela?.numeroParcela ? `#${boleto.parcela.numeroParcela}` : "-"}</td>
                    <td className="max-w-sm truncate px-4 py-3 font-mono text-xs text-slate-600">{boleto.linhaDigitavel}</td>
                    <td className="px-4 py-3 text-slate-600">{formatCurrency(boleto.valor)}</td>
                    <td className="px-4 py-3 text-slate-600">{formatDate(boleto.vencimento)}</td>
                    <td className="px-4 py-3">
                      <StatusBadge status={boleto.status} label={statusBoletoLabels[boleto.status]} />
                    </td>
                    <td className="px-4 py-3 text-slate-600">
                      {boleto.confirmacaoLeitura ? "Leitura" : "-"} · {boleto.confirmacaoRecebimento ? "Recebimento" : "-"}
                    </td>
                  </tr>
                );
              })}
            </tbody>
          </table>

          {boletos.length === 0 ? (
            <div className="px-4 py-8 text-center text-sm text-slate-500">Nenhum boleto encontrado para esta categoria.</div>
          ) : null}
        </CardContent>
      </Card>
    </div>
  );
}
