<?php

namespace App\Support\Billing;

use App\Enums\InteracaoCanal;
use App\Enums\ParcelaStatus;
use App\Enums\PopChecklistStatus;
use App\Enums\SerasaEtapa;
use App\Enums\SerasaStatus;
use App\Enums\TarefaPrioridade;
use App\Enums\TarefaStatus;
use App\Models\Boleto;
use App\Models\BoletoDdaControle;
use App\Models\Interacao;
use App\Models\Parcela;
use App\Models\PopFinanceiroChecklist;
use App\Models\SerasaOcorrencia;
use App\Models\Tarefa;
use Carbon\CarbonInterface;

class PopFinanceiroService
{
    /**
     * @return array<string, int>
     */
    public function runDaily(CarbonInterface $referenceDate): array
    {
        $referenceDate = $referenceDate->copy()->startOfDay();

        $summary = [
            'parcelas_processadas' => 0,
            'checklists_criados' => 0,
            'tarefas_criadas' => 0,
            'interacoes_criadas' => 0,
            'serasa_ocorrencias_criadas' => 0,
            'dda_controles_criados' => 0,
        ];

        $parcelas = Parcela::query()
            ->whereIn('status', [
                ParcelaStatus::Pendente->value,
                ParcelaStatus::Enviada->value,
                ParcelaStatus::EmAtraso->value,
                ParcelaStatus::EmNegativacao->value,
            ])
            ->whereDate('vencimento', '<', $referenceDate->toDateString())
            ->with(['cobranca.cliente', 'boleto'])
            ->get();

        foreach ($parcelas as $parcela) {
            $summary['parcelas_processadas']++;

            $diasAtraso = $parcela->vencimento?->copy()->startOfDay()->diffInDays($referenceDate, false) ?? 0;

            if ($diasAtraso <= 0) {
                continue;
            }

            if ($parcela->boleto instanceof Boleto) {
                $dda = BoletoDdaControle::firstOrCreate(
                    ['boleto_id' => $parcela->boleto->id],
                    ['status' => 'PENDENTE_VERIFICACAO']
                );

                if ($dda->wasRecentlyCreated) {
                    $summary['dda_controles_criados']++;
                }
            }

            $summary['checklists_criados'] += $this->upsertChecklistForAtraso($parcela, $referenceDate, $diasAtraso);
            $summary['tarefas_criadas'] += $this->createTarefasForAtraso($parcela, $referenceDate, $diasAtraso);
            $summary['interacoes_criadas'] += $this->createInteracoesSistemaForAtraso($parcela, $referenceDate, $diasAtraso);
            $summary['serasa_ocorrencias_criadas'] += $this->registerSerasaSkeleton($parcela, $referenceDate, $diasAtraso);
        }

        return $summary;
    }

    private function upsertChecklistForAtraso(Parcela $parcela, CarbonInterface $referenceDate, int $diasAtraso): int
    {
        $created = 0;

        $created += $this->upsertChecklist(
            parcela: $parcela,
            referenceDate: $referenceDate,
            etapa: 'ATRASO_VENDEDOR_IMEDIATO',
            titulo: 'Acionar vendedor responsável imediatamente',
            descricao: 'Parcela em atraso: abrir tratativa imediata com vendedor responsável.',
            acaoCanal: InteracaoCanal::Sistema->value,
            escalonamentoNivel: 'VENDEDOR',
            slaLimiteEm: $referenceDate->copy()->addDay(),
        );

        $created += $this->upsertChecklist(
            parcela: $parcela,
            referenceDate: $referenceDate,
            etapa: 'SLA_24H',
            titulo: 'Validar resolução em até 24h',
            descricao: 'Se não resolver em 24h, escalar para próximo nível.',
            acaoCanal: InteracaoCanal::Sistema->value,
            escalonamentoNivel: 'SLA_24H',
            slaLimiteEm: $referenceDate->copy()->addDay(),
        );

        if ($diasAtraso >= 2) {
            $created += $this->upsertChecklist(
                parcela: $parcela,
                referenceDate: $referenceDate,
                etapa: 'ESCALONAMENTO_LARISSA',
                titulo: 'Escalonar para Larissa',
                descricao: 'SLA estourado. Escalonamento interno para Larissa (skeleton, sem envio automático).',
                acaoCanal: InteracaoCanal::Sistema->value,
                escalonamentoNivel: 'LARISSA',
                slaLimiteEm: $referenceDate->copy()->addDay(),
            );
        }

        if ($diasAtraso >= 3) {
            $created += $this->upsertChecklist(
                parcela: $parcela,
                referenceDate: $referenceDate,
                etapa: 'ESCALONAMENTO_EDIVALDO',
                titulo: 'Escalonar para Edivaldo',
                descricao: 'Sem resolução após escalonamento inicial. Encaminhar para Edivaldo (skeleton).',
                acaoCanal: InteracaoCanal::Sistema->value,
                escalonamentoNivel: 'EDIVALDO',
                slaLimiteEm: $referenceDate->copy()->addDay(),
            );
        }

        if ($diasAtraso >= 10) {
            $created += $this->upsertChecklist(
                parcela: $parcela,
                referenceDate: $referenceDate,
                etapa: 'COBRANCA_10_DIAS',
                titulo: 'Executar régua de 10 dias',
                descricao: 'Acionar cobrança de 10 dias com roteiro padronizado (somente checklist).',
                acaoCanal: InteracaoCanal::Sistema->value,
                escalonamentoNivel: 'OPERACAO',
                slaLimiteEm: $referenceDate->copy()->addDay(),
            );

            if (($diasAtraso - 10) % 5 === 0 && $diasAtraso > 10) {
                $created += $this->upsertChecklist(
                    parcela: $parcela,
                    referenceDate: $referenceDate,
                    etapa: 'REPETICAO_5_DIAS',
                    titulo: 'Repetição automática após 10 dias (a cada 5)',
                    descricao: 'Checklist de repetição operacional da cobrança após 10 dias.',
                    acaoCanal: InteracaoCanal::Sistema->value,
                    escalonamentoNivel: 'OPERACAO',
                    slaLimiteEm: $referenceDate->copy()->addDay(),
                );
            }
        }

        if ($diasAtraso >= 30) {
            $created += $this->upsertChecklist(
                parcela: $parcela,
                referenceDate: $referenceDate,
                etapa: 'NEGATIVACAO_30_DIAS',
                titulo: 'Negativação formal aos 30 dias',
                descricao: 'Preparar documentação para negativação formal e execução no fluxo SERASA.',
                acaoCanal: InteracaoCanal::Sistema->value,
                escalonamentoNivel: 'SERASA',
                slaLimiteEm: $referenceDate->copy()->addDay(),
            );
        }

        return $created;
    }

    private function createTarefasForAtraso(Parcela $parcela, CarbonInterface $referenceDate, int $diasAtraso): int
    {
        $created = 0;

        $tarefa = Tarefa::firstOrCreate(
            [
                'tipo' => 'POP_ATRASO',
                'cobranca_id' => $parcela->cobranca_id,
                'cliente_id' => $parcela->cobranca?->cliente_id,
                'titulo' => 'POP: tratar parcela em atraso #'.$parcela->numero,
                'status' => TarefaStatus::Aberta->value,
                'vence_em' => $referenceDate->copy()->endOfDay(),
            ],
            [
                'assigned_to_id' => $parcela->cobranca?->responsavel_id,
                'descricao' => 'Ação operacional POP criada automaticamente (sem envio automático).',
                'prioridade' => $diasAtraso >= 30 ? TarefaPrioridade::Critica->value : TarefaPrioridade::Alta->value,
                'metadata' => [
                    'reference_date' => $referenceDate->toDateString(),
                    'dias_atraso' => $diasAtraso,
                ],
            ]
        );

        if ($tarefa->wasRecentlyCreated) {
            $created++;
        }

        return $created;
    }

    private function createInteracoesSistemaForAtraso(Parcela $parcela, CarbonInterface $referenceDate, int $diasAtraso): int
    {
        $interacao = Interacao::firstOrCreate(
            [
                'cliente_id' => $parcela->cobranca?->cliente_id,
                'cobranca_id' => $parcela->cobranca_id,
                'parcela_id' => $parcela->id,
                'canal' => InteracaoCanal::Sistema->value,
                'resultado' => 'POP_REGISTRADO',
                'assunto' => 'POP financeiro - atraso #'.$parcela->numero,
                'ocorreu_em' => $referenceDate->copy()->setTime(8, 0),
            ],
            [
                'descricao' => 'Registro operacional automático da régua POP (sem envio real de e-mail/WhatsApp).',
                'metadata' => [
                    'reference_date' => $referenceDate->toDateString(),
                    'dias_atraso' => $diasAtraso,
                    'skeleton_only' => true,
                ],
            ]
        );

        return $interacao->wasRecentlyCreated ? 1 : 0;
    }

    private function registerSerasaSkeleton(Parcela $parcela, CarbonInterface $referenceDate, int $diasAtraso): int
    {
        $created = 0;

        if ($diasAtraso >= 10) {
            $notificacao = SerasaOcorrencia::firstOrCreate(
                [
                    'cobranca_id' => $parcela->cobranca_id,
                    'etapa' => SerasaEtapa::Notificacao->value,
                ],
                [
                    'responsavel_id' => $parcela->cobranca?->responsavel_id,
                    'status' => SerasaStatus::Pendente->value,
                    'documento_devedor' => $parcela->cobranca?->cliente?->documento,
                    'valor_negativado' => $parcela->valor,
                    'data_limite_regularizacao' => $referenceDate->copy()->addDays(10)->toDateString(),
                    'agendado_para' => $referenceDate->copy()->endOfDay(),
                    'observacoes' => 'Skeleton POP: notificação SERASA preparada, sem integração externa automática.',
                    'metadata' => ['skeleton_only' => true],
                ]
            );

            if ($notificacao->wasRecentlyCreated) {
                $created++;
            }
        }

        if ($diasAtraso >= 30) {
            $negativacao = SerasaOcorrencia::firstOrCreate(
                [
                    'cobranca_id' => $parcela->cobranca_id,
                    'etapa' => SerasaEtapa::NegativacaoFormal->value,
                ],
                [
                    'responsavel_id' => $parcela->cobranca?->responsavel_id,
                    'status' => SerasaStatus::Pendente->value,
                    'documento_devedor' => $parcela->cobranca?->cliente?->documento,
                    'valor_negativado' => $parcela->valor,
                    'data_limite_regularizacao' => $referenceDate->toDateString(),
                    'agendado_para' => $referenceDate->copy()->endOfDay(),
                    'observacoes' => 'Skeleton POP: negativação formal preparada para execução manual.',
                    'metadata' => ['skeleton_only' => true],
                ]
            );

            if ($negativacao->wasRecentlyCreated) {
                $created++;
            }
        }

        return $created;
    }

    private function upsertChecklist(
        Parcela $parcela,
        CarbonInterface $referenceDate,
        string $etapa,
        string $titulo,
        string $descricao,
        ?string $acaoCanal,
        ?string $escalonamentoNivel,
        ?CarbonInterface $slaLimiteEm = null,
    ): int {
        $item = PopFinanceiroChecklist::updateOrCreate(
            [
                'reference_date' => $referenceDate->toDateString(),
                'cobranca_id' => $parcela->cobranca_id,
                'parcela_id' => $parcela->id,
                'etapa' => $etapa,
            ],
            [
                'cliente_id' => $parcela->cobranca?->cliente_id,
                'boleto_id' => $parcela->boleto?->id,
                'assigned_to_id' => $parcela->cobranca?->responsavel_id,
                'status' => PopChecklistStatus::Pendente->value,
                'acao_canal' => $acaoCanal,
                'escalonamento_nivel' => $escalonamentoNivel,
                'titulo' => $titulo,
                'descricao' => $descricao,
                'sla_limite_em' => $slaLimiteEm,
                'metadata' => [
                    'dias_atraso' => $parcela->vencimento?->copy()->startOfDay()->diffInDays($referenceDate, false),
                    'skeleton_only' => true,
                ],
            ]
        );

        return $item->wasRecentlyCreated ? 1 : 0;
    }
}
