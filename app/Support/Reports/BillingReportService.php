<?php

namespace App\Support\Reports;

use App\Enums\CobrancaStatus;
use App\Enums\ParcelaStatus;
use App\Enums\TarefaStatus;
use App\Models\Cobranca;
use App\Models\Parcela;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;

class BillingReportService
{
    public function resumo(array $filters): array
    {
        $parcelas = $this->parcelasBase($filters);
        $cobrancas = $this->cobrancasBase($filters);

        $openStatuses = [
            ParcelaStatus::Pendente->value,
            ParcelaStatus::Enviada->value,
            ParcelaStatus::EmAtraso->value,
            ParcelaStatus::EmNegativacao->value,
        ];

        return [
            'periodo' => [
                'de' => $filters['de'] ?? null,
                'ate' => $filters['ate'] ?? null,
            ],
            'cobrancas' => [
                'total' => (clone $cobrancas)->count(),
                'casos_criticos' => (clone $cobrancas)
                    ->where(function (Builder $query) {
                        $query
                            ->whereIn('status', [
                                CobrancaStatus::Cobranca30Dias->value,
                                CobrancaStatus::Negativacao->value,
                            ])
                            ->orWhere('prioridade', '>=', 4);
                    })
                    ->count(),
            ],
            'valores' => [
                'em_aberto' => $this->sum((clone $parcelas)->whereIn('parcelas.status', $openStatuses), 'parcelas.valor'),
                'em_atraso' => $this->sum((clone $parcelas)->whereIn('parcelas.status', [
                    ParcelaStatus::EmAtraso->value,
                    ParcelaStatus::EmNegativacao->value,
                ]), 'parcelas.valor'),
                'pago' => $this->sum((clone $parcelas)->where('parcelas.status', ParcelaStatus::Paga->value), 'COALESCE(parcelas.valor_pago, parcelas.valor)'),
                'previsao_recebimento' => $this->sum(
                    (clone $parcelas)
                        ->whereIn('parcelas.status', [ParcelaStatus::Pendente->value, ParcelaStatus::Enviada->value])
                        ->whereDate('parcelas.vencimento', '>=', now()->toDateString()),
                    'parcelas.valor',
                ),
            ],
        ];
    }

    public function inadimplencia(array $filters): array
    {
        $paginator = (clone $this->parcelasBase($filters))
            ->with(['cobranca.cliente', 'cobranca.responsavel'])
            ->where(function (Builder $query) {
                $query
                    ->whereIn('parcelas.status', [
                        ParcelaStatus::EmAtraso->value,
                        ParcelaStatus::EmNegativacao->value,
                    ])
                    ->orWhere(function (Builder $query) {
                        $query
                            ->whereDate('parcelas.vencimento', '<', now()->toDateString())
                            ->whereIn('parcelas.status', [
                                ParcelaStatus::Pendente->value,
                                ParcelaStatus::Enviada->value,
                            ]);
                    });
            })
            ->orderBy('parcelas.vencimento')
            ->paginate($filters['per_page'] ?? 20);

        return $this->paginate($paginator, fn (Parcela $parcela): array => [
            'parcela_id' => $parcela->id,
            'cobranca_id' => $parcela->cobranca_id,
            'codigo_cobranca' => $parcela->cobranca?->codigo,
            'cliente' => $parcela->cobranca?->cliente?->nome,
            'responsavel' => $parcela->cobranca?->responsavel?->name,
            'numero' => $parcela->numero,
            'valor' => (float) $parcela->valor,
            'vencimento' => $parcela->vencimento?->toDateString(),
            'status' => $parcela->status?->value,
            'dias_em_atraso' => max(0, (int) $parcela->vencimento?->diffInDays(now(), false)),
        ]);
    }

    public function previsaoRecebimento(array $filters): array
    {
        return (clone $this->parcelasBase($filters))
            ->whereIn('parcelas.status', [ParcelaStatus::Pendente->value, ParcelaStatus::Enviada->value])
            ->selectRaw("to_char(date_trunc('month', parcelas.vencimento::timestamp), 'YYYY-MM') as mes")
            ->selectRaw('COUNT(*) as parcelas')
            ->selectRaw('COALESCE(SUM(parcelas.valor), 0) as valor_previsto')
            ->groupByRaw("date_trunc('month', parcelas.vencimento::timestamp)")
            ->orderByRaw("date_trunc('month', parcelas.vencimento::timestamp)")
            ->get()
            ->map(fn (object $row): array => [
                'mes' => $row->mes,
                'parcelas' => (int) $row->parcelas,
                'valor_previsto' => round((float) $row->valor_previsto, 2),
            ])
            ->all();
    }

    public function produtividade(array $filters): array
    {
        $interacoes = DB::table('interacoes')
            ->select('user_id')
            ->selectRaw('COUNT(*) as total_interacoes')
            ->whereNotNull('user_id')
            ->groupBy('user_id');

        $this->applyPeriod($interacoes, 'ocorreu_em', $filters);

        $tarefas = DB::table('tarefas')
            ->select('assigned_to_id')
            ->selectRaw('COUNT(*) as total_tarefas_concluidas')
            ->where('status', TarefaStatus::Concluida->value)
            ->whereNotNull('assigned_to_id')
            ->groupBy('assigned_to_id');

        $this->applyPeriod($tarefas, 'concluida_em', $filters);

        return DB::table('users')
            ->leftJoinSub($interacoes, 'interacoes', 'interacoes.user_id', '=', 'users.id')
            ->leftJoinSub($tarefas, 'tarefas', 'tarefas.assigned_to_id', '=', 'users.id')
            ->select('users.id', 'users.name', 'users.email')
            ->selectRaw('COALESCE(interacoes.total_interacoes, 0) as interacoes')
            ->selectRaw('COALESCE(tarefas.total_tarefas_concluidas, 0) as tarefas_concluidas')
            ->whereRaw('COALESCE(interacoes.total_interacoes, 0) + COALESCE(tarefas.total_tarefas_concluidas, 0) > 0')
            ->orderByDesc('interacoes')
            ->orderByDesc('tarefas_concluidas')
            ->get()
            ->map(fn (object $row): array => [
                'user_id' => $row->id,
                'nome' => $row->name,
                'email' => $row->email,
                'interacoes' => (int) $row->interacoes,
                'tarefas_concluidas' => (int) $row->tarefas_concluidas,
            ])
            ->all();
    }

    public function evolucaoTemporal(array $filters): array
    {
        $cobrancas = DB::table('cobrancas')
            ->selectRaw("to_char(date_trunc('month', data_emissao::timestamp), 'YYYY-MM') as mes")
            ->selectRaw('COUNT(*) as cobrancas')
            ->selectRaw('COALESCE(SUM(valor_total), 0) as valor_emitido')
            ->groupByRaw("date_trunc('month', data_emissao::timestamp)")
            ->orderByRaw("date_trunc('month', data_emissao::timestamp)");

        $this->applyCobrancaFilters($cobrancas, $filters, 'cobrancas');
        $this->applyPeriod($cobrancas, 'data_emissao', $filters);

        $pagamentos = DB::table('parcelas')
            ->join('cobrancas', 'cobrancas.id', '=', 'parcelas.cobranca_id')
            ->where('parcelas.status', ParcelaStatus::Paga->value)
            ->whereNotNull('parcelas.paga_em')
            ->selectRaw("to_char(date_trunc('month', parcelas.paga_em), 'YYYY-MM') as mes")
            ->selectRaw('COUNT(*) as parcelas_pagas')
            ->selectRaw('COALESCE(SUM(COALESCE(parcelas.valor_pago, parcelas.valor)), 0) as valor_pago')
            ->groupByRaw("date_trunc('month', parcelas.paga_em)")
            ->orderByRaw("date_trunc('month', parcelas.paga_em)");

        $this->applyCobrancaFilters($pagamentos, $filters, 'cobrancas');
        $this->applyPeriod($pagamentos, 'parcelas.paga_em', $filters);

        return [
            'cobrancas' => $cobrancas->get()->map(fn (object $row): array => [
                'mes' => $row->mes,
                'cobrancas' => (int) $row->cobrancas,
                'valor_emitido' => round((float) $row->valor_emitido, 2),
            ])->all(),
            'pagamentos' => $pagamentos->get()->map(fn (object $row): array => [
                'mes' => $row->mes,
                'parcelas_pagas' => (int) $row->parcelas_pagas,
                'valor_pago' => round((float) $row->valor_pago, 2),
            ])->all(),
        ];
    }

    private function cobrancasBase(array $filters): Builder
    {
        $query = Cobranca::query();

        if ($filters['de'] ?? null) {
            $query->whereDate('data_emissao', '>=', $filters['de']);
        }

        if ($filters['ate'] ?? null) {
            $query->whereDate('data_emissao', '<=', $filters['ate']);
        }

        if ($filters['cliente_id'] ?? null) {
            $query->where('cliente_id', $filters['cliente_id']);
        }

        if ($filters['usuario_id'] ?? null) {
            $query->where('responsavel_id', $filters['usuario_id']);
        }

        if ($filters['categoria'] ?? null) {
            $query->where('categoria', $filters['categoria']);
        }

        if ($filters['cobranca_status'] ?? null) {
            $query->where('status', $filters['cobranca_status']);
        }

        return $query;
    }

    private function parcelasBase(array $filters): Builder
    {
        $query = Parcela::query()
            ->select('parcelas.*')
            ->join('cobrancas', 'cobrancas.id', '=', 'parcelas.cobranca_id');

        if ($filters['de'] ?? null) {
            $query->whereDate('parcelas.vencimento', '>=', $filters['de']);
        }

        if ($filters['ate'] ?? null) {
            $query->whereDate('parcelas.vencimento', '<=', $filters['ate']);
        }

        if ($filters['cliente_id'] ?? null) {
            $query->where('cobrancas.cliente_id', $filters['cliente_id']);
        }

        if ($filters['usuario_id'] ?? null) {
            $query->where('cobrancas.responsavel_id', $filters['usuario_id']);
        }

        if ($filters['categoria'] ?? null) {
            $query->where('cobrancas.categoria', $filters['categoria']);
        }

        if ($filters['parcela_status'] ?? null) {
            $query->where('parcelas.status', $filters['parcela_status']);
        }

        return $query;
    }

    private function applyPeriod(QueryBuilder $query, string $column, array $filters): void
    {
        if ($filters['de'] ?? null) {
            $query->whereDate($column, '>=', $filters['de']);
        }

        if ($filters['ate'] ?? null) {
            $query->whereDate($column, '<=', $filters['ate']);
        }
    }

    private function applyCobrancaFilters(QueryBuilder $query, array $filters, string $table): void
    {
        if ($filters['cliente_id'] ?? null) {
            $query->where("$table.cliente_id", $filters['cliente_id']);
        }

        if ($filters['usuario_id'] ?? null) {
            $query->where("$table.responsavel_id", $filters['usuario_id']);
        }

        if ($filters['categoria'] ?? null) {
            $query->where("$table.categoria", $filters['categoria']);
        }

        if ($filters['cobranca_status'] ?? null) {
            $query->where("$table.status", $filters['cobranca_status']);
        }
    }

    private function sum(Builder $query, string $expression): float
    {
        return round((float) $query->select(DB::raw("COALESCE(SUM($expression), 0) as total"))->value('total'), 2);
    }

    /**
     * @param  callable(Parcela): array<string, mixed>  $mapper
     */
    private function paginate(LengthAwarePaginator $paginator, callable $mapper): array
    {
        return [
            'data' => $paginator->getCollection()->map($mapper)->values(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
        ];
    }
}
