export const demoClientes = [
  {
    id: "demo-cliente-1",
    nome: "Condomínio Residencial Atlântico",
    tipoCliente: "CONDOMINIO",
    documento: "12.345.678/0001-90",
    responsavelFinanceiro: "Larissa Menezes",
    email: "financeiro@atlantico.com.br",
    telefone: "(21) 98888-1000",
    whatsapp: "(21) 98888-1000",
    endereco: "Rua das Palmeiras, 120 - Rio de Janeiro/RJ",
    observacoes: "Cliente parcelado com fluxo preventivo ativo.",
    statusAtivo: true,
    criadoEm: "2026-05-01T12:00:00.000Z",
    atualizadoEm: "2026-05-28T12:00:00.000Z",
    cobrancas: [
      {
        id: "demo-cobranca-1",
        clienteId: "demo-cliente-1",
        tipoCobranca: "PARCELADO",
        valorTotal: 12840.5,
        statusCobranca: "PREVENTIVA",
        dataEmissao: "2026-05-01T12:00:00.000Z",
        dataVencimentoPrincipal: "2026-06-02T12:00:00.000Z",
        proximaAcao: "Reenviar parcela 2 e confirmar recebimento.",
        dataProximaAcao: "2026-05-29T12:00:00.000Z",
        observacoes: "Parcelamento em 3 vezes.",
        criadoEm: "2026-05-01T12:00:00.000Z",
        atualizadoEm: "2026-05-28T12:00:00.000Z",
        parcelas: [
          {
            id: "demo-parcela-1",
            numeroParcela: 1,
            valor: 4280.17,
            vencimento: "2026-05-15T12:00:00.000Z",
            status: "PAGA",
            dataPagamento: "2026-05-15T14:00:00.000Z",
            boleto: null,
          },
          {
            id: "demo-parcela-2",
            numeroParcela: 2,
            valor: 4280.17,
            vencimento: "2026-06-02T12:00:00.000Z",
            status: "ENVIADA",
            dataEnvio: "2026-05-27T12:00:00.000Z",
            boleto: {
              id: "demo-boleto-2",
              linhaDigitavel: "00190.00009 01234.002009 56789.000001 1 00000000428017",
              codigoBarras: "00191000000000428017000000000000000000000002",
              valor: 4280.17,
              vencimento: "2026-06-02T12:00:00.000Z",
              status: "ENVIADO",
              dataEmissao: "2026-05-27T12:00:00.000Z",
              dataEnvio: "2026-05-27T13:00:00.000Z",
              confirmacaoLeitura: true,
              confirmacaoRecebimento: false,
            },
          },
          {
            id: "demo-parcela-3",
            numeroParcela: 3,
            valor: 4280.16,
            vencimento: "2026-07-02T12:00:00.000Z",
            status: "PENDENTE",
            boleto: null,
          },
        ],
      },
    ],
    interacoes: [
      {
        id: "demo-interacao-1",
        canal: "EMAIL",
        resultado: "ENVIADO",
        responsavelNome: "Setor Financeiro",
        dataHora: "2026-05-27T12:12:00.000Z",
        observacoes: "Boleto enviado com solicitação de confirmação de leitura.",
      },
      {
        id: "demo-interacao-2",
        canal: "WHATSAPP",
        resultado: "RECEBIDO",
        responsavelNome: "Larissa Menezes",
        dataHora: "2026-05-27T13:34:00.000Z",
        observacoes: "Mensagem recebida pelo responsável financeiro.",
      },
    ],
    tarefas: [
      {
        id: "demo-tarefa-1",
        tipo: "REENVIO_PREVENTIVO",
        descricao: "Reenviar boleto e confirmar recebimento.",
        dataAgendada: "2026-05-29T12:00:00.000Z",
        status: "ABERTA",
        responsavelNome: "Setor Financeiro",
      },
    ],
  },
  {
    id: "demo-cliente-2",
    nome: "Norte Engenharia e Serviços Ltda.",
    tipoCliente: "PJ",
    documento: "45.667.891/0001-54",
    responsavelFinanceiro: "Camila Rocha",
    email: "financeiro@norteengenharia.com.br",
    telefone: "(21) 97777-3000",
    whatsapp: "(21) 97777-3000",
    endereco: "Av. Rio Branco, 500 - Rio de Janeiro/RJ",
    observacoes: "Caso crítico de cobrança formal.",
    statusAtivo: true,
    criadoEm: "2026-04-20T12:00:00.000Z",
    atualizadoEm: "2026-05-28T12:00:00.000Z",
    cobrancas: [
      {
        id: "demo-cobranca-2",
        clienteId: "demo-cliente-2",
        tipoCobranca: "PARCELADO",
        valorTotal: 16800,
        statusCobranca: "COBRANCA_30_DIAS",
        dataEmissao: "2026-04-20T12:00:00.000Z",
        dataVencimentoPrincipal: "2026-04-27T12:00:00.000Z",
        proximaAcao: "Preparar evidências e negativação.",
        dataProximaAcao: "2026-05-28T12:00:00.000Z",
        observacoes: "Cliente ciente da dívida, sem previsão confirmada.",
        criadoEm: "2026-04-20T12:00:00.000Z",
        atualizadoEm: "2026-05-28T12:00:00.000Z",
        parcelas: [
          {
            id: "demo-parcela-4",
            numeroParcela: 1,
            valor: 5600,
            vencimento: "2026-04-27T12:00:00.000Z",
            status: "EM_NEGATIVACAO",
            boleto: null,
          },
          {
            id: "demo-parcela-5",
            numeroParcela: 2,
            valor: 5600,
            vencimento: "2026-05-27T12:00:00.000Z",
            status: "ATRASADA",
            boleto: null,
          },
          {
            id: "demo-parcela-6",
            numeroParcela: 3,
            valor: 5600,
            vencimento: "2026-06-27T12:00:00.000Z",
            status: "PENDENTE",
            boleto: null,
          },
        ],
      },
    ],
    interacoes: [
      {
        id: "demo-interacao-3",
        canal: "LIGACAO",
        resultado: "SEM_RESPOSTA",
        responsavelNome: "Edivaldo Santos",
        dataHora: "2026-05-27T18:05:00.000Z",
        observacoes: "Última tentativa antes da medida formal.",
      },
    ],
    tarefas: [
      {
        id: "demo-tarefa-2",
        tipo: "NEGATIVACAO",
        descricao: "Preparar documentação para negativação.",
        dataAgendada: "2026-05-28T12:00:00.000Z",
        status: "ABERTA",
        responsavelNome: "Setor Financeiro",
      },
    ],
  },
];

export const demoCobrancas = demoClientes.flatMap((cliente) =>
  cliente.cobrancas.map((cobranca) => ({
    ...cobranca,
    cliente,
    tarefas: cliente.tarefas,
    interacoes: cliente.interacoes,
    boletos: cobranca.parcelas.flatMap((parcela) => (parcela.boleto ? [parcela.boleto] : [])),
  })),
);

export const demoParcelas = demoCobrancas.flatMap((cobranca) =>
  cobranca.parcelas.map((parcela) => ({ ...parcela, cobranca })),
);

export const demoBoletos = demoParcelas.flatMap((parcela) =>
  parcela.boleto ? [{ ...parcela.boleto, parcela, cobranca: parcela.cobranca }] : [],
);

export const demoInteracoes = demoClientes.flatMap((cliente) =>
  cliente.interacoes.map((interacao) => ({ ...interacao, cliente })),
);

export const demoTarefas = demoClientes.flatMap((cliente) =>
  cliente.tarefas.map((tarefa) => ({ ...tarefa, clienteId: cliente.id, cliente })),
);
