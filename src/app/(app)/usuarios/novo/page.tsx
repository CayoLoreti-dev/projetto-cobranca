import { UsuarioForm } from "@/components/forms/usuario-form";
import { Card, CardContent, CardHeader } from "@/components/ui/card";

export default function NovoUsuarioPage() {
  return (
    <div className="space-y-6">
      <header>
        <h1 className="text-2xl font-semibold tracking-tight text-slate-950">Novo usuário</h1>
        <p className="text-sm text-slate-500">Conta interna do setor</p>
      </header>

      <Card>
        <CardHeader>
          <h2 className="text-base font-semibold text-slate-950">Dados de acesso</h2>
        </CardHeader>
        <CardContent>
          <UsuarioForm />
        </CardContent>
      </Card>
    </div>
  );
}
