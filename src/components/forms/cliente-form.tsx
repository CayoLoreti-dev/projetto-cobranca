"use client";

import { zodResolver } from "@hookform/resolvers/zod";
import { useMutation } from "@tanstack/react-query";
import { Loader2, Save } from "lucide-react";
import { useRouter } from "next/navigation";
import { useForm } from "react-hook-form";
import { z } from "zod";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Select } from "@/components/ui/select";
import { Textarea } from "@/components/ui/textarea";
import { tipoClienteLabels } from "@/lib/constants";
import { clienteCreateSchema } from "@/lib/validations";

type ClienteFormData = z.input<typeof clienteCreateSchema>;

async function createCliente(data: ClienteFormData) {
  const response = await fetch("/api/clientes", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(data),
  });

  const payload = await response.json();

  if (!response.ok) {
    throw new Error(payload?.error?.message ?? "Não foi possível salvar o cliente.");
  }

  return payload.data as { id: string };
}

export function ClienteForm() {
  const router = useRouter();
  const {
    register,
    handleSubmit,
    formState: { errors },
  } = useForm<ClienteFormData>({
    resolver: zodResolver(clienteCreateSchema),
    defaultValues: {
      tipoCliente: "CONDOMINIO",
      statusAtivo: true,
    },
  });

  const mutation = useMutation({
    mutationFn: createCliente,
    onSuccess: (cliente) => {
      router.push(`/clientes/${cliente.id}`);
      router.refresh();
    },
  });

  return (
    <form className="grid gap-5" onSubmit={handleSubmit((data) => mutation.mutate(data))}>
      <div className="grid gap-4 md:grid-cols-2">
        <Field label="Nome" error={errors.nome?.message}>
          <Input {...register("nome")} placeholder="Condomínio Residencial Atlântico" />
        </Field>

        <Field label="Tipo" error={errors.tipoCliente?.message}>
          <Select {...register("tipoCliente")}>
            {Object.entries(tipoClienteLabels).map(([value, label]) => (
              <option key={value} value={value}>
                {label}
              </option>
            ))}
          </Select>
        </Field>

        <Field label="CPF/CNPJ" error={errors.documento?.message}>
          <Input {...register("documento")} placeholder="12.345.678/0001-90" />
        </Field>

        <Field label="Responsável financeiro" error={errors.responsavelFinanceiro?.message}>
          <Input {...register("responsavelFinanceiro")} placeholder="Nome do responsável" />
        </Field>

        <Field label="E-mail" error={errors.email?.message}>
          <Input type="email" {...register("email")} placeholder="financeiro@cliente.com.br" />
        </Field>

        <Field label="Telefone" error={errors.telefone?.message}>
          <Input {...register("telefone")} placeholder="(21) 99999-0000" />
        </Field>

        <Field label="WhatsApp" error={errors.whatsapp?.message}>
          <Input {...register("whatsapp")} placeholder="(21) 99999-0000" />
        </Field>

        <Field label="Endereço" error={errors.endereco?.message}>
          <Input {...register("endereco")} placeholder="Rua, número, cidade" />
        </Field>
      </div>

      <Field label="Observações" error={errors.observacoes?.message}>
        <Textarea {...register("observacoes")} placeholder="Dados importantes para cobrança" />
      </Field>

      {mutation.error ? <p className="rounded-md bg-rose-50 px-3 py-2 text-sm text-rose-700">{mutation.error.message}</p> : null}

      <div className="flex justify-end">
        <Button type="submit" disabled={mutation.isPending}>
          {mutation.isPending ? <Loader2 className="h-4 w-4 animate-spin" aria-hidden="true" /> : <Save className="h-4 w-4" aria-hidden="true" />}
          Salvar cliente
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
