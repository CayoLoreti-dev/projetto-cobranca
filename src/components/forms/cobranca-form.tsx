"use client";

import { zodResolver } from "@hookform/resolvers/zod";
import { useMutation } from "@tanstack/react-query";
import { CalendarPlus, Loader2, Plus, Save, Trash2 } from "lucide-react";
import { useRouter } from "next/navigation";
import { useFieldArray, useForm, useWatch } from "react-hook-form";
import { z } from "zod";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Select } from "@/components/ui/select";
import { Textarea } from "@/components/ui/textarea";
import { tipoCobrancaLabels } from "@/lib/constants";
import { cobrancaCreateSchema } from "@/lib/validations";

type CobrancaFormInput = z.input<typeof cobrancaCreateSchema>;

async function createCobranca(data: CobrancaFormInput) {
  const response = await fetch("/api/cobrancas", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(data),
  });
  const payload = await response.json();

  if (!response.ok) {
    throw new Error(payload?.error?.message ?? "Não foi possível criar a cobrança.");
  }

  return payload.data as { id: string };
}

function addMonths(dateValue: string, months: number) {
  const date = new Date(`${dateValue}T12:00:00`);
  date.setMonth(date.getMonth() + months);
  return date.toISOString().slice(0, 10);
}

export function CobrancaForm({
  clientes,
}: {
  clientes: Array<{ id: string; nome: string; documento: string }>;
}) {
  const router = useRouter();
  const {
    control,
    register,
    handleSubmit,
    getValues,
    setValue,
    formState: { errors },
  } = useForm<CobrancaFormInput>({
    resolver: zodResolver(cobrancaCreateSchema),
    defaultValues: {
      clienteId: clientes[0]?.id ?? "",
      tipoCobranca: "AVISTA",
      valorTotal: 0,
      dataEmissao: new Date().toISOString().slice(0, 10),
      dataVencimentoPrincipal: new Date().toISOString().slice(0, 10),
      gerarBoletos: true,
      parcelas: [
        {
          numeroParcela: 1,
          valor: 0,
          vencimento: new Date().toISOString().slice(0, 10),
          status: "PENDENTE",
        },
      ],
    },
  });

  const { fields, append, remove, replace } = useFieldArray({ control, name: "parcelas" });
  const tipoCobranca = useWatch({ control, name: "tipoCobranca" });

  const mutation = useMutation({
    mutationFn: createCobranca,
    onSuccess: (cobranca) => {
      router.push(`/cobrancas/${cobranca.id}`);
      router.refresh();
    },
  });

  function gerarParcelas(quantidade: number) {
    const valorTotal = Number(getValues("valorTotal")) || 0;
    const vencimento = String(getValues("dataVencimentoPrincipal") || new Date().toISOString().slice(0, 10));
    const valorBase = Math.floor((valorTotal / quantidade) * 100) / 100;
    const parcelas = Array.from({ length: quantidade }, (_, index) => {
      const isLast = index === quantidade - 1;
      const valor = isLast ? Number((valorTotal - valorBase * (quantidade - 1)).toFixed(2)) : valorBase;

      return {
        numeroParcela: index + 1,
        valor,
        vencimento: addMonths(vencimento, index),
        status: "PENDENTE" as const,
        observacoes: "",
      };
    });

    replace(parcelas);
    setValue("tipoCobranca", quantidade === 1 ? "AVISTA" : "PARCELADO");
  }

  return (
    <form className="grid gap-5" onSubmit={handleSubmit((data) => mutation.mutate(data))}>
      <div className="grid gap-4 lg:grid-cols-3">
        <Field label="Cliente" error={errors.clienteId?.message}>
          <Select {...register("clienteId")}>
            {clientes.map((cliente) => (
              <option key={cliente.id} value={cliente.id}>
                {cliente.nome} - {cliente.documento}
              </option>
            ))}
          </Select>
        </Field>

        <Field label="Tipo" error={errors.tipoCobranca?.message}>
          <Select {...register("tipoCobranca")}>
            {Object.entries(tipoCobrancaLabels).map(([value, label]) => (
              <option key={value} value={value}>
                {label}
              </option>
            ))}
          </Select>
        </Field>

        <Field label="Valor total" error={errors.valorTotal?.message}>
          <Input type="number" step="0.01" min="0" {...register("valorTotal")} />
        </Field>

        <Field label="Emissão" error={errors.dataEmissao?.message}>
          <Input type="date" {...register("dataEmissao")} />
        </Field>

        <Field label="Vencimento principal" error={errors.dataVencimentoPrincipal?.message}>
          <Input type="date" {...register("dataVencimentoPrincipal")} />
        </Field>

        <Field label="Próxima ação" error={errors.proximaAcao?.message}>
          <Input {...register("proximaAcao")} placeholder="Confirmar envio do boleto" />
        </Field>
      </div>

      <Field label="Observações" error={errors.observacoes?.message}>
        <Textarea {...register("observacoes")} placeholder="Condições combinadas, DDA, nota fiscal ou política interna" />
      </Field>

      <section className="rounded-lg border border-slate-200 bg-white">
        <div className="flex flex-col gap-3 border-b border-slate-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
          <div>
            <h2 className="text-base font-semibold text-slate-950">Parcelas</h2>
            <p className="text-sm text-slate-500">{tipoCobranca === "PARCELADO" ? "Parcelamento" : "Cobrança única"}</p>
          </div>
          <div className="flex flex-wrap gap-2">
            <Button type="button" variant="secondary" size="sm" onClick={() => gerarParcelas(1)}>
              <CalendarPlus className="h-4 w-4" aria-hidden="true" />
              À vista
            </Button>
            <Button type="button" variant="secondary" size="sm" onClick={() => gerarParcelas(3)}>
              <CalendarPlus className="h-4 w-4" aria-hidden="true" />
              3 parcelas
            </Button>
            <Button type="button" variant="secondary" size="sm" onClick={() => gerarParcelas(6)}>
              <CalendarPlus className="h-4 w-4" aria-hidden="true" />
              6 parcelas
            </Button>
            <Button
              type="button"
              variant="secondary"
              size="sm"
              onClick={() =>
                append({
                  numeroParcela: fields.length + 1,
                  valor: 0,
                  vencimento: new Date().toISOString().slice(0, 10),
                  status: "PENDENTE",
                })
              }
            >
              <Plus className="h-4 w-4" aria-hidden="true" />
              Parcela
            </Button>
          </div>
        </div>

        <div className="overflow-x-auto">
          <table className="min-w-full divide-y divide-slate-200 text-left text-sm">
            <thead className="bg-slate-50 text-xs uppercase text-slate-500">
              <tr>
                <th className="px-4 py-3">Nº</th>
                <th className="px-4 py-3">Valor</th>
                <th className="px-4 py-3">Vencimento</th>
                <th className="px-4 py-3">Status</th>
                <th className="px-4 py-3">Ações</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-200 bg-white">
              {fields.map((field, index) => (
                <tr key={field.id}>
                  <td className="px-4 py-3">
                    <Input className="w-20" type="number" {...register(`parcelas.${index}.numeroParcela`)} />
                  </td>
                  <td className="px-4 py-3">
                    <Input className="w-36" type="number" step="0.01" {...register(`parcelas.${index}.valor`)} />
                  </td>
                  <td className="px-4 py-3">
                    <Input className="w-40" type="date" {...register(`parcelas.${index}.vencimento`)} />
                  </td>
                  <td className="px-4 py-3">
                    <Select className="w-40" {...register(`parcelas.${index}.status`)}>
                      <option value="PENDENTE">Pendente</option>
                      <option value="ENVIADA">Enviada</option>
                      <option value="PAGA">Paga</option>
                      <option value="ATRASADA">Atrasada</option>
                    </Select>
                  </td>
                  <td className="px-4 py-3">
                    <Button type="button" variant="ghost" size="icon" title="Remover parcela" onClick={() => remove(index)}>
                      <Trash2 className="h-4 w-4" aria-hidden="true" />
                    </Button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
        {errors.parcelas ? <p className="px-5 py-3 text-sm text-rose-600">{errors.parcelas.message}</p> : null}
      </section>

      {mutation.error ? <p className="rounded-md bg-rose-50 px-3 py-2 text-sm text-rose-700">{mutation.error.message}</p> : null}

      <div className="flex justify-end">
        <Button type="submit" disabled={mutation.isPending}>
          {mutation.isPending ? <Loader2 className="h-4 w-4 animate-spin" aria-hidden="true" /> : <Save className="h-4 w-4" aria-hidden="true" />}
          Criar cobrança
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
