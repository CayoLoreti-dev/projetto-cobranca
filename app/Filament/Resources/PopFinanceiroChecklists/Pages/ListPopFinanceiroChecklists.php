<?php

namespace App\Filament\Resources\PopFinanceiroChecklists\Pages;

use App\Filament\Resources\PopFinanceiroChecklists\PopFinanceiroChecklistResource;
use App\Support\Billing\PopFinanceiroService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListPopFinanceiroChecklists extends ListRecords
{
    protected static string $resource = PopFinanceiroChecklistResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('processarPopHoje')
                ->label('Processar POP hoje')
                ->icon('heroicon-o-play')
                ->color('success')
                ->requiresConfirmation()
                ->authorize(fn (): bool => auth()->user()?->can('pop_financeiro.update') ?? false)
                ->action(function (): void {
                    $summary = app(PopFinanceiroService::class)->runDaily(now());

                    Notification::make()
                        ->title('POP financeiro processado')
                        ->body("Checklists criados: {$summary['checklists_criados']} | Tarefas: {$summary['tarefas_criadas']} | SERASA: {$summary['serasa_ocorrencias_criadas']}")
                        ->success()
                        ->send();
                }),
            CreateAction::make(),
        ];
    }
}
