<?php

namespace App\Filament\Widgets;

use App\Enums\CobrancaStatus;
use App\Enums\ParcelaStatus;
use App\Models\Cliente;
use App\Models\Cobranca;
use App\Models\Parcela;
use App\Models\Tarefa;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class BillingOverviewWidget extends StatsOverviewWidget
{
    protected static ?int $sort = -3;

    protected ?string $heading = 'Visão geral';

    protected ?string $description = 'Resumo rápido da operação de cobrança.';

    protected int|array|null $columns = [
        '@xl' => 4,
        '!@lg' => 4,
    ];

    protected function getStats(): array
    {
        $metrics = Cache::remember(
            'filament.dashboard.billing-overview.metrics',
            now()->addSeconds(30),
            function (): array {
                $parcelasAbertas = Parcela::query()
                    ->whereIn('status', [
                        ParcelaStatus::Pendente->value,
                        ParcelaStatus::Enviada->value,
                        ParcelaStatus::EmAtraso->value,
                        ParcelaStatus::EmNegativacao->value,
                    ]);

                $emAberto = (clone $parcelasAbertas)->sum('valor');
                $emAtraso = (clone $parcelasAbertas)
                    ->where(function ($query): void {
                        $query
                            ->whereIn('status', [ParcelaStatus::EmAtraso->value, ParcelaStatus::EmNegativacao->value])
                            ->orWhereDate('vencimento', '<', now()->toDateString());
                    })
                    ->sum('valor');

                return [
                    'clientes_ativos' => Cliente::query()->where('status', 'ATIVO')->count(),
                    'cobrancas_abertas' => Cobranca::query()->whereNotIn('status', [
                        CobrancaStatus::Paga->value,
                        CobrancaStatus::Cancelada->value,
                        CobrancaStatus::Arquivada->value,
                    ])->count(),
                    'valor_em_aberto' => (float) $emAberto,
                    'valor_em_atraso' => (float) $emAtraso,
                    'demandas_abertas' => Tarefa::query()->whereIn('status', ['ABERTA', 'EM_ANDAMENTO', 'ATRASADA'])->count(),
                ];
            }
        );

        return [
            Stat::make('Clientes ativos', $metrics['clientes_ativos'])
                ->description('Base operacional')
                ->icon(Heroicon::OutlinedUsers)
                ->color('gray'),
            Stat::make('Cobranças abertas', $metrics['cobrancas_abertas'])
                ->description('Em acompanhamento')
                ->icon(Heroicon::OutlinedWallet)
                ->color('gray'),
            Stat::make('Valor em aberto', 'R$ '.number_format($metrics['valor_em_aberto'], 2, ',', '.'))
                ->description('Parcelas ainda não baixadas')
                ->icon(Heroicon::OutlinedBanknotes)
                ->color('gray'),
            Stat::make('Em atraso', 'R$ '.number_format($metrics['valor_em_atraso'], 2, ',', '.'))
                ->description('Prioridade de cobrança')
                ->icon(Heroicon::OutlinedExclamationTriangle)
                ->color('danger'),
            Stat::make('Demandas abertas', $metrics['demandas_abertas'])
                ->description('Pendências de equipe')
                ->icon(Heroicon::OutlinedQueueList)
                ->color('gray'),
        ];
    }
}
