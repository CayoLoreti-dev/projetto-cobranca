"use client";

import { zodResolver } from "@hookform/resolvers/zod";
import { useMutation } from "@tanstack/react-query";
import { Loader2, Save } from "lucide-react";
import { useRouter } from "next/navigation";
import { useForm, useWatch } from "react-hook-form";
import { z } from "zod";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Select } from "@/components/ui/select";
import { perfilLabels } from "@/lib/constants";
import { usuarioCreateSchema } from "@/lib/validations";

type UsuarioFormData = z.input<typeof usuarioCreateSchema>;
type PerfilKey = "ADMIN" | "FINANCEIRO" | "OPERADOR" | "LEITURA";

const permissoesPadrao: Record<PerfilKey, string[]> = {
  ADMIN: ["usuarios:write", "clientes:write", "cobrancas:write", "auditoria:read"],
  FINANCEIRO: ["clientes:write", "cobrancas:write", "tarefas:write"],
  OPERADOR: ["clientes:read", "interacoes:write", "tarefas:write"],
  LEITURA: ["clientes:read", "cobrancas:read"],
};

async function createUsuario(data: UsuarioFormData) {
  const response = await fetch("/api/usuarios", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(data),
  });
  const payload = await response.json();

  if (!response.ok) {
    throw new Error(payload?.error?.message ?? "Não foi possível criar o usuário.");
  }

  return payload.data as { id: string };
}

export function UsuarioForm() {
  const router = useRouter();
  const {
    control,
    register,
    handleSubmit,
    setValue,
    formState: { errors },
  } = useForm<UsuarioFormData>({
    resolver: zodResolver(usuarioCreateSchema),
    defaultValues: {
      perfil: "OPERADOR",
      permissoes: permissoesPadrao.OPERADOR,
      ativo: true,
    },
  });
  const perfil = (useWatch({ control, name: "perfil" }) ?? "OPERADOR") as PerfilKey;
  const perfilRegister = register("perfil", {
    onChange: (event) => {
      const nextPerfil = event.target.value as PerfilKey;
      setValue("permissoes", permissoesPadrao[nextPerfil]);
    },
  });

  const mutation = useMutation({
    mutationFn: createUsuario,
    onSuccess: () => {
      router.push("/usuarios");
      router.refresh();
    },
  });

  return (
    <form className="grid gap-5" onSubmit={handleSubmit((data) => mutation.mutate(data))}>
      <div className="grid gap-4 md:grid-cols-2">
        <Field label="Nome" error={errors.nome?.message}>
          <Input {...register("nome")} placeholder="Larissa Menezes" />
        </Field>

        <Field label="E-mail" error={errors.email?.message}>
          <Input type="email" {...register("email")} placeholder="usuario@apvistoria.local" />
        </Field>

        <Field label="Senha inicial" error={errors.senha?.message}>
          <Input type="password" {...register("senha")} placeholder="mínimo 8 caracteres" />
        </Field>

        <Field label="Perfil" error={errors.perfil?.message}>
          <Select {...perfilRegister}>
            {Object.entries(perfilLabels).map(([value, label]) => (
              <option key={value} value={value}>
                {label}
              </option>
            ))}
          </Select>
        </Field>
      </div>

      <section className="rounded-md border border-slate-200 bg-slate-50 p-4">
        <p className="text-sm font-medium text-slate-950">Permissões do perfil {perfilLabels[perfil]}</p>
        <div className="mt-3 flex flex-wrap gap-2">
          {permissoesPadrao[perfil].map((permissao) => (
            <span key={permissao} className="rounded-full bg-white px-3 py-1 text-xs font-medium text-slate-700 ring-1 ring-slate-200">
              {permissao}
            </span>
          ))}
        </div>
      </section>

      {mutation.error ? <p className="rounded-md bg-rose-50 px-3 py-2 text-sm text-rose-700">{mutation.error.message}</p> : null}

      <div className="flex justify-end">
        <Button type="submit" disabled={mutation.isPending}>
          {mutation.isPending ? <Loader2 className="h-4 w-4 animate-spin" aria-hidden="true" /> : <Save className="h-4 w-4" aria-hidden="true" />}
          Criar usuário
        </Button>
      </div>
    </form>
  );
}

function Field({
  label,
  error,
  children,
}: {
  label: string;
  error?: string;
  children: React.ReactNode;
}) {
  return (
    <div className="space-y-2">
      <Label>{label}</Label>
      {children}
      {error ? <p className="text-sm text-rose-600">{error}</p> : null}
    </div>
  );
}
