import { z } from "zod";

export const tipoClienteValues = ["PF", "PJ", "CONDOMINIO"] as const;
export const perfilUsuarioValues = ["ADMIN", "FINANCEIRO", "OPERADOR", "LEITURA"] as const;
export const tipoCobrancaValues = ["AVISTA", "PARCELADO"] as const;
export const statusCobrancaValues = [
  "RASCUNHO",
  "EMITIDA",
  "ENVIADA",
  "PREVENTIVA",
  "COBRANCA_5_DIAS",
  "COBRANCA_10_DIAS",
  "COBRANCA_30_DIAS",
  "NEGATIVACAO",
  "PAGA",
  "CANCELADA",
] as const;
export const statusParcelaValues = [
  "PENDENTE",
  "ENVIADA",
  "PAGA",
  "ATRASADA",
  "EM_NEGATIVACAO",
  "CANCELADA",
] as const;
export const statusBoletoValues = [
  "EMITIDO",
  "ENVIADO",
  "LIDO",
  "RECEBIDO",
  "PAGO",
  "VENCIDO",
  "CANCELADO",
] as const;
export const canalInteracaoValues = [
  "EMAIL",
  "WHATSAPP",
  "LIGACAO",
  "SISTEMA",
  "MANUAL_INTERNO",
] as const;
export const resultadoInteracaoValues = [
  "ENVIADO",
  "LIDO",
  "RECEBIDO",
  "RESPONDIDO",
  "SEM_RESPOSTA",
  "PROMESSA_PAGAMENTO",
  "CONTESTACAO",
  "TELEFONE_INVALIDO",
  "REGISTRO_INTERNO",
] as const;
export const tipoTarefaValues = [
  "ENVIO_BOLETO",
  "REENVIO_PREVENTIVO",
  "COBRANCA_5_DIAS",
  "COBRANCA_10_DIAS",
  "COBRANCA_30_DIAS",
  "NEGATIVACAO",
  "LIGACAO_CONSULTIVA",
  "CONFERIR_DDA",
  "EMITIR_NOTA",
] as const;
export const statusTarefaValues = [
  "ABERTA",
  "EM_ANDAMENTO",
  "CONCLUIDA",
  "CANCELADA",
  "ATRASADA",
] as const;

const documentoRegex = /^(\d{11}|\d{14}|\d{3}\.\d{3}\.\d{3}-\d{2}|\d{2}\.\d{3}\.\d{3}\/\d{4}-\d{2})$/;
const phoneRegex = /^[0-9()+\-\s]{8,20}$/;

export const paginationSchema = z.object({
  page: z.coerce.number().int().positive().default(1),
  pageSize: z.coerce.number().int().min(1).max(100).default(20),
  busca: z.string().trim().optional(),
});

export const clienteCreateSchema = z.object({
  nome: z.string().trim().min(3, "Informe o nome do cliente."),
  tipoCliente: z.enum(tipoClienteValues),
  documento: z.string().trim().regex(documentoRegex, "Informe CPF ou CNPJ com 11/14 dígitos ou máscara válida."),
  responsavelFinanceiro: z.string().trim().min(2, "Informe o responsável financeiro."),
  email: z.string().trim().toLowerCase().email("Informe um e-mail válido."),
  telefone: z.string().trim().regex(phoneRegex, "Informe um telefone válido."),
  whatsapp: z.string().trim().regex(phoneRegex, "Informe um WhatsApp válido.").optional().or(z.literal("")),
  endereco: z.string().trim().optional().or(z.literal("")),
  observacoes: z.string().trim().optional().or(z.literal("")),
  statusAtivo: z.coerce.boolean().default(true),
});

export const clienteUpdateSchema = clienteCreateSchema.partial();

export const usuarioCreateSchema = z.object({
  nome: z.string().trim().min(2, "Informe o nome do usuário."),
  email: z.string().trim().toLowerCase().email("Informe um e-mail válido."),
  senha: z.string().min(8, "A senha precisa ter pelo menos 8 caracteres."),
  perfil: z.enum(perfilUsuarioValues).default("OPERADOR"),
  permissoes: z.array(z.string().trim().min(1)).default([]),
  ativo: z.coerce.boolean().default(true),
});

export const usuarioUpdateSchema = usuarioCreateSchema.partial().extend({
  senha: z.string().min(8, "A senha precisa ter pelo menos 8 caracteres.").optional().or(z.literal("")),
});

export const parcelaInputSchema = z.object({
  numeroParcela: z.coerce.number().int().positive("A parcela precisa ter número positivo."),
  valor: z.coerce.number().positive("A parcela precisa ter valor maior que zero."),
  vencimento: z.coerce.date(),
  status: z.enum(statusParcelaValues).default("PENDENTE"),
  observacoes: z.string().trim().optional().or(z.literal("")),
});

export const parcelaUpdateSchema = parcelaInputSchema.partial().extend({
  dataEnvio: z.coerce.date().optional().nullable(),
  dataReenvio: z.coerce.date().optional().nullable(),
  dataPagamento: z.coerce.date().optional().nullable(),
});

export const cobrancaCreateSchema = z
  .object({
    clienteId: z.string().trim().min(1, "Selecione o cliente."),
    tipoCobranca: z.enum(tipoCobrancaValues),
    valorTotal: z.coerce.number().positive("Informe um valor total maior que zero."),
    statusCobranca: z.enum(statusCobrancaValues).default("EMITIDA"),
    dataEmissao: z.coerce.date().default(() => new Date()),
    dataVencimentoPrincipal: z.coerce.date(),
    responsavelAtualId: z.string().trim().optional().or(z.literal("")),
    proximaAcao: z.string().trim().optional().or(z.literal("")),
    dataProximaAcao: z.coerce.date().optional(),
    observacoes: z.string().trim().optional().or(z.literal("")),
    parcelas: z.array(parcelaInputSchema).min(1, "Crie pelo menos uma parcela."),
    gerarBoletos: z.coerce.boolean().default(true),
  })
  .superRefine((value, ctx) => {
    if (value.tipoCobranca === "AVISTA" && value.parcelas.length !== 1) {
      ctx.addIssue({
        code: "custom",
        path: ["parcelas"],
        message: "Cobrança à vista deve ter exatamente uma parcela.",
      });
    }

    if (value.tipoCobranca === "PARCELADO" && value.parcelas.length < 2) {
      ctx.addIssue({
        code: "custom",
        path: ["parcelas"],
        message: "Cobrança parcelada deve nascer com todas as parcelas.",
      });
    }

    const somaParcelas = value.parcelas.reduce((sum, parcela) => sum + parcela.valor, 0);
    const diferenca = Math.abs(somaParcelas - value.valorTotal);

    if (diferenca > 0.01) {
      ctx.addIssue({
        code: "custom",
        path: ["valorTotal"],
        message: "A soma das parcelas precisa bater com o valor total.",
      });
    }
  });

export const cobrancaUpdateSchema = z.object({
  statusCobranca: z.enum(statusCobrancaValues).optional(),
  responsavelAtualId: z.string().trim().optional().or(z.literal("")),
  proximaAcao: z.string().trim().optional().or(z.literal("")),
  dataProximaAcao: z.coerce.date().optional(),
  observacoes: z.string().trim().optional().or(z.literal("")),
});

const boletoBaseSchema = z.object({
  parcelaId: z.string().trim().optional().or(z.literal("")),
  cobrancaId: z.string().trim().optional().or(z.literal("")),
  codigoBarras: z.string().trim().min(20, "Informe o código de barras."),
  linhaDigitavel: z.string().trim().min(20, "Informe a linha digitável."),
  valor: z.coerce.number().positive("Informe o valor do boleto."),
  vencimento: z.coerce.date(),
  status: z.enum(statusBoletoValues).default("EMITIDO"),
  dataEnvio: z.coerce.date().optional(),
  confirmacaoLeitura: z.coerce.boolean().default(false),
  confirmacaoRecebimento: z.coerce.boolean().default(false),
  pdfUrl: z.string().trim().url("Informe uma URL válida.").optional().or(z.literal("")),
  observacoes: z.string().trim().optional().or(z.literal("")),
});

export const boletoCreateSchema = boletoBaseSchema
  .refine((value) => value.parcelaId || value.cobrancaId, {
    path: ["parcelaId"],
    message: "Vincule o boleto a uma parcela ou cobrança.",
  });

export const boletoUpdateSchema = boletoBaseSchema.partial();

export const interacaoCreateSchema = z.object({
  clienteId: z.string().trim().min(1, "Informe o cliente."),
  cobrancaId: z.string().trim().optional().or(z.literal("")),
  parcelaId: z.string().trim().optional().or(z.literal("")),
  canal: z.enum(canalInteracaoValues),
  resultado: z.enum(resultadoInteracaoValues),
  responsavelId: z.string().trim().optional().or(z.literal("")),
  responsavelNome: z.string().trim().min(2, "Informe quem fez a interação."),
  dataHora: z.coerce.date().default(() => new Date()),
  observacoes: z.string().trim().min(3, "Explique brevemente o que aconteceu."),
});

export const tarefaCreateSchema = z.object({
  clienteId: z.string().trim().min(1, "Informe o cliente."),
  cobrancaId: z.string().trim().optional().or(z.literal("")),
  tipo: z.enum(tipoTarefaValues),
  descricao: z.string().trim().min(3, "Descreva a tarefa."),
  dataAgendada: z.coerce.date(),
  status: z.enum(statusTarefaValues).default("ABERTA"),
  responsavelId: z.string().trim().optional().or(z.literal("")),
  responsavelNome: z.string().trim().min(2, "Informe o responsável."),
});

export const tarefaUpdateSchema = tarefaCreateSchema.partial().extend({
  concluidaEm: z.coerce.date().optional().nullable(),
});

export type ClienteCreateInput = z.infer<typeof clienteCreateSchema>;
export type CobrancaCreateInput = z.infer<typeof cobrancaCreateSchema>;
export type InteracaoCreateInput = z.infer<typeof interacaoCreateSchema>;
