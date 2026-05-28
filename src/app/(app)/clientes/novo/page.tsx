import { ClienteForm } from "@/components/forms/cliente-form";
import { Card, CardContent, CardHeader } from "@/components/ui/card";

export default function NovoClientePage() {
  return (
    <div className="space-y-6">
      <header>
        <h1 className="text-2xl font-semibold tracking-tight text-slate-950">Novo cliente</h1>
        <p className="text-sm text-slate-500">Cadastro financeiro</p>
      </header>

      <Card>
        <CardHeader>
          <h2 className="text-base font-semibold text-slate-950">Dados do cliente</h2>
        </CardHeader>
        <CardContent>
          <ClienteForm />
        </CardContent>
      </Card>
    </div>
  );
}
