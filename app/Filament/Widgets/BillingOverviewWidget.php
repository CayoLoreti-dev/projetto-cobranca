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
            Stat::make('Clientes ativos', Cliente::query()->where('status', 'ATIVO')->count())
                ->description('Base operacional')
                ->icon(Heroicon::OutlinedUsers)
                ->color('gray'),
            Stat::make('Cobranças abertas', Cobranca::query()->whereNotIn('status', [
                CobrancaStatus::Paga->value,
                CobrancaStatus::Cancelada->value,
                CobrancaStatus::Arquivada->value,
            ])->count())
                ->description('Em acompanhamento')
                ->icon(Heroicon::OutlinedWallet)
                ->color('gray'),
            Stat::make('Valor em aberto', 'R$ '.number_format((float) $emAberto, 2, ',', '.'))
                ->description('Parcelas ainda não baixadas')
                ->icon(Heroicon::OutlinedBanknotes)
                ->color('gray'),
            Stat::make('Em atraso', 'R$ '.number_format((float) $emAtraso, 2, ',', '.'))
                ->description('Prioridade de cobrança')
                ->icon(Heroicon::OutlinedExclamationTriangle)
                ->color('danger'),
            Stat::make('Demandas abertas', Tarefa::query()->whereIn('status', ['ABERTA', 'EM_ANDAMENTO', 'ATRASADA'])->count())
                ->description('Pendências de equipe')
                ->icon(Heroicon::OutlinedQueueList)
                ->color('gray'),
        ];
    }
}
