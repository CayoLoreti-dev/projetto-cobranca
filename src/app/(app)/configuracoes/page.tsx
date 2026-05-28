import { Card, CardContent, CardHeader } from "@/components/ui/card";

const regras = [
  ["Reenvio preventivo", "5 dias antes do vencimento"],
  ["Cobrança inicial", "5 dias após vencimento"],
  ["Cobrança reforçada", "10 dias após vencimento"],
  ["Cobrança formal", "30 dias após vencimento"],
  ["Canais", "E-mail, WhatsApp, ligação, sistema e manual interno"],
];

export default function ConfiguracoesPage() {
  return (
    <div className="space-y-6">
      <header>
        <h1 className="text-2xl font-semibold tracking-tight text-slate-950">Configurações</h1>
        <p className="text-sm text-slate-500">Políticas operacionais</p>
      </header>

      <Card>
        <CardHeader>
          <h2 className="text-base font-semibold text-slate-950">Fluxo de cobrança</h2>
        </CardHeader>
        <CardContent>
          <div className="grid gap-3 md:grid-cols-2">
            {regras.map(([label, value]) => (
              <div key={label} className="rounded-md border border-slate-200 p-4">
                <p className="text-sm text-slate-500">{label}</p>
                <p className="mt-2 text-sm font-medium text-slate-950">{value}</p>
              </div>
            ))}
          </div>
        </CardContent>
      </Card>
    </div>
  );
}
