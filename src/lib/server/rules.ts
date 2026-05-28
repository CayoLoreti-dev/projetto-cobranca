import type {
  StatusCobranca,
  StatusParcela,
  TipoTarefa,
} from "@/generated/prisma/client";

const dayInMs = 24 * 60 * 60 * 1000;

export function startOfDay(date: Date) {
  return new Date(date.getFullYear(), date.getMonth(), date.getDate());
}

export function addDays(date: Date, days: number) {
  const next = new Date(date);
  next.setDate(next.getDate() + days);
  return next;
}

export function daysBetween(from: Date, to: Date) {
  return Math.floor((startOfDay(to).getTime() - startOfDay(from).getTime()) / dayInMs);
}

export function resolveParcelaStatus(vencimento: Date, dataPagamento?: Date | null): StatusParcela {
  if (dataPagamento) {
    return "PAGA";
  }

  const diasAtraso = daysBetween(vencimento, new Date());

  if (diasAtraso >= 30) {
    return "EM_NEGATIVACAO";
  }

  if (diasAtraso > 0) {
    return "ATRASADA";
  }

  return "PENDENTE";
}

export function resolveCobrancaStatus(vencimento: Date, jaPaga = false): StatusCobranca {
  if (jaPaga) {
    return "PAGA";
  }

  const diasAtraso = daysBetween(vencimento, new Date());
  const diasAteVencer = daysBetween(new Date(), vencimento);

  if (diasAtraso >= 30) {
    return "COBRANCA_30_DIAS";
  }

  if (diasAtraso >= 10) {
    return "COBRANCA_10_DIAS";
  }

  if (diasAtraso >= 5) {
    return "COBRANCA_5_DIAS";
  }

  if (diasAteVencer <= 5) {
    return "PREVENTIVA";
  }

  return "ENVIADA";
}

export function gerarTarefasDeCobranca(vencimento: Date) {
  const tarefas: Array<{ tipo: TipoTarefa; descricao: string; dataAgendada: Date }> = [
    {
      tipo: "ENVIO_BOLETO",
      descricao: "Enviar boleto e registrar confirmação de recebimento.",
      dataAgendada: addDays(vencimento, -10),
    },
    {
      tipo: "REENVIO_PREVENTIVO",
      descricao: "Reenviar boleto 5 dias antes do vencimento por e-mail e WhatsApp.",
      dataAgendada: addDays(vencimento, -5),
    },
    {
      tipo: "COBRANCA_5_DIAS",
      descricao: "Executar cobrança inicial de 5 dias: e-mail prioritário, WhatsApp e registro.",
      dataAgendada: addDays(vencimento, 5),
    },
    {
      tipo: "COBRANCA_10_DIAS",
      descricao: "Executar cobrança reforçada de 10 dias e informar política interna de SERASA.",
      dataAgendada: addDays(vencimento, 10),
    },
    {
      tipo: "COBRANCA_30_DIAS",
      descricao: "Preparar cobrança formal final, evidências e possível negativação.",
      dataAgendada: addDays(vencimento, 30),
    },
  ];

  return tarefas;
}

export function gerarLinhaDigitavelDemo(numero: number) {
  const base = String(numero).padStart(3, "0");
  return `00190.00009 01234.${base}009 56789.000001 1 00000000000000`;
}

export function gerarCodigoBarrasDemo(numero: number) {
  return `0019100000000000000001234${String(numero).padStart(12, "0")}`;
}
