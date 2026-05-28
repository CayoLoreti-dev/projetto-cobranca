export const tipoClienteLabels: Record<string, string> = {
  PF: "Pessoa física",
  PJ: "Pessoa jurídica",
  CONDOMINIO: "Condomínio",
};

export const tipoCobrancaLabels: Record<string, string> = {
  AVISTA: "À vista",
  PARCELADO: "Parcelado",
};

export const perfilLabels: Record<string, string> = {
  ADMIN: "Administrador",
  FINANCEIRO: "Financeiro",
  OPERADOR: "Operador",
  LEITURA: "Leitura",
};

export const statusCobrancaLabels: Record<string, string> = {
  RASCUNHO: "Rascunho",
  EMITIDA: "Emitida",
  ENVIADA: "Enviada",
  PREVENTIVA: "Preventiva",
  COBRANCA_5_DIAS: "5 dias",
  COBRANCA_10_DIAS: "10 dias",
  COBRANCA_30_DIAS: "30 dias",
  NEGATIVACAO: "Negativação",
  PAGA: "Paga",
  CANCELADA: "Cancelada",
};

export const statusParcelaLabels: Record<string, string> = {
  PENDENTE: "Pendente",
  ENVIADA: "Enviada",
  PAGA: "Paga",
  ATRASADA: "Atrasada",
  EM_NEGATIVACAO: "Em negativação",
  CANCELADA: "Cancelada",
};

export const statusBoletoLabels: Record<string, string> = {
  EMITIDO: "Emitido",
  ENVIADO: "Enviado",
  LIDO: "Leitura confirmada",
  RECEBIDO: "Recebimento confirmado",
  PAGO: "Pago",
  VENCIDO: "Vencido",
  CANCELADO: "Cancelado",
};

export const canalLabels: Record<string, string> = {
  EMAIL: "E-mail",
  WHATSAPP: "WhatsApp",
  LIGACAO: "Ligação",
  SISTEMA: "Sistema",
  MANUAL_INTERNO: "Manual interno",
};

export const resultadoLabels: Record<string, string> = {
  ENVIADO: "Enviado",
  LIDO: "Lido",
  RECEBIDO: "Recebido",
  RESPONDIDO: "Respondido",
  SEM_RESPOSTA: "Sem resposta",
  PROMESSA_PAGAMENTO: "Promessa de pagamento",
  CONTESTACAO: "Contestação",
  TELEFONE_INVALIDO: "Telefone inválido",
  REGISTRO_INTERNO: "Registro interno",
};

export const tipoTarefaLabels: Record<string, string> = {
  ENVIO_BOLETO: "Envio de boleto",
  REENVIO_PREVENTIVO: "Reenvio preventivo",
  COBRANCA_5_DIAS: "Cobrança 5 dias",
  COBRANCA_10_DIAS: "Cobrança 10 dias",
  COBRANCA_30_DIAS: "Cobrança 30 dias",
  NEGATIVACAO: "Negativação",
  LIGACAO_CONSULTIVA: "Ligação consultiva",
  CONFERIR_DDA: "Conferir DDA",
  EMITIR_NOTA: "Emitir nota",
};

export const statusTarefaLabels: Record<string, string> = {
  ABERTA: "Aberta",
  EM_ANDAMENTO: "Em andamento",
  CONCLUIDA: "Concluída",
  CANCELADA: "Cancelada",
  ATRASADA: "Atrasada",
};

export const statusTone: Record<string, string> = {
  RASCUNHO: "bg-slate-100 text-slate-700 ring-slate-200",
  EMITIDA: "bg-sky-50 text-sky-700 ring-sky-200",
  ENVIADA: "bg-indigo-50 text-indigo-700 ring-indigo-200",
  PREVENTIVA: "bg-teal-50 text-teal-700 ring-teal-200",
  COBRANCA_5_DIAS: "bg-amber-50 text-amber-800 ring-amber-200",
  COBRANCA_10_DIAS: "bg-orange-50 text-orange-800 ring-orange-200",
  COBRANCA_30_DIAS: "bg-rose-50 text-rose-800 ring-rose-200",
  NEGATIVACAO: "bg-zinc-200 text-zinc-950 ring-zinc-300",
  PAGA: "bg-emerald-50 text-emerald-700 ring-emerald-200",
  CANCELADA: "bg-slate-200 text-slate-800 ring-slate-300",
  PENDENTE: "bg-slate-100 text-slate-700 ring-slate-200",
  ATRASADA: "bg-rose-50 text-rose-800 ring-rose-200",
  EM_NEGATIVACAO: "bg-zinc-200 text-zinc-950 ring-zinc-300",
  ABERTA: "bg-sky-50 text-sky-700 ring-sky-200",
  EM_ANDAMENTO: "bg-amber-50 text-amber-800 ring-amber-200",
  CONCLUIDA: "bg-emerald-50 text-emerald-700 ring-emerald-200",
};
