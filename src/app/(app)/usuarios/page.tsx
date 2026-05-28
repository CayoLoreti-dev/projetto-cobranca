import { Plus } from "lucide-react";
import Link from "next/link";
import { StatusBadge } from "@/components/status-badge";
import { Button } from "@/components/ui/button";
import { Card, CardContent } from "@/components/ui/card";
import { perfilLabels } from "@/lib/constants";
import { formatDate } from "@/lib/formatters";
import { listarUsuariosPage } from "@/lib/server/queries";

export const dynamic = "force-dynamic";

export default async function UsuariosPage() {
  const usuarios = await listarUsuariosPage();

  return (
    <div className="space-y-6">
      <header className="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <h1 className="text-2xl font-semibold tracking-tight text-slate-950">Usuários do setor</h1>
          <p className="text-sm text-slate-500">Contas, perfis e demandas por responsável</p>
        </div>
        <Link href="/usuarios/novo">
          <Button>
            <Plus className="h-4 w-4" aria-hidden="true" />
            Novo usuário
          </Button>
        </Link>
      </header>

      <Card>
        <CardContent className="overflow-x-auto p-0">
          <table className="min-w-full divide-y divide-slate-200 text-left text-sm">
            <thead className="bg-slate-50 text-xs uppercase text-slate-500">
              <tr>
                <th className="px-4 py-3">Usuário</th>
                <th className="px-4 py-3">Perfil</th>
                <th className="px-4 py-3">Demandas abertas</th>
                <th className="px-4 py-3">Cobranças responsáveis</th>
                <th className="px-4 py-3">Status</th>
                <th className="px-4 py-3">Criado em</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-200 bg-white">
              {usuarios.map((usuario) => (
                <tr key={usuario.id} className="hover:bg-slate-50">
                  <td className="px-4 py-3">
                    <p className="font-medium text-slate-950">{usuario.nome}</p>
                    <p className="text-xs text-slate-500">{usuario.email}</p>
                  </td>
                  <td className="px-4 py-3 text-slate-600">{perfilLabels[usuario.perfil]}</td>
                  <td className="px-4 py-3 text-slate-600">{usuario.tarefas.length}</td>
                  <td className="px-4 py-3 text-slate-600">{usuario.cobrancasResponsaveis.length}</td>
                  <td className="px-4 py-3">
                    <StatusBadge status={usuario.ativo ? "PAGA" : "CANCELADA"} label={usuario.ativo ? "Ativo" : "Inativo"} />
                  </td>
                  <td className="px-4 py-3 text-slate-600">{formatDate(usuario.criadoEm)}</td>
                </tr>
              ))}
            </tbody>
          </table>
        </CardContent>
      </Card>
    </div>
  );
}
