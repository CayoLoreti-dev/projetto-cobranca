<?php

namespace App\Filament\Resources\PopFinanceiroChecklists\Tables;

use App\Enums\PopChecklistStatus;
use App\Models\PopFinanceiroChecklist;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PopFinanceiroChecklistsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('reference_date', 'desc')
            ->columns([
                IconColumn::make('feito')
                    ->label('Feito')
                    ->boolean()
                    ->getStateUsing(fn (PopFinanceiroChecklist $record): bool => $record->status === PopChecklistStatus::Concluido)
                    ->trueIcon(Heroicon::CheckCircle)
                    ->falseIcon(Heroicon::OutlinedClock)
                    ->trueColor('success')
                    ->falseColor('warning'),
                TextColumn::make('reference_date')
                    ->label('Data')
                    ->date()
                    ->sortable(),
                TextColumn::make('etapa')
                    ->label('Etapa')
                    ->badge()
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->searchable(),
                TextColumn::make('escalonamento_nivel')
                    ->label('Escalonamento')
                    ->searchable()
                    ->placeholder('-'),
                TextColumn::make('acao_canal')
                    ->label('Canal')
                    ->searchable()
                    ->placeholder('-'),
                TextColumn::make('titulo')
                    ->label('Checklist')
                    ->searchable()
                    ->limit(60),
                TextColumn::make('cliente.nome')
                    ->label('Cliente')
                    ->searchable(),
                TextColumn::make('cobranca.codigo')
                    ->label('Cobrança')
                    ->searchable(),
                TextColumn::make('parcela.numero')
                    ->label('Parcela')
                    ->numeric()
                    ->placeholder('-'),
                TextColumn::make('responsavel.name')
                    ->label('Responsável')
                    ->searchable()
                    ->placeholder('-'),
                TextColumn::make('sla_limite_em')
                    ->label('SLA')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('-'),
                TextColumn::make('concluido_em')
                    ->label('Concluído em')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('-'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(PopChecklistStatus::class),
                SelectFilter::make('escalonamento_nivel')
                    ->label('Escalonamento')
                    ->options([
                        'VENDEDOR' => 'Vendedor',
                        'SLA_24H' => 'SLA 24h',
                        'LARISSA' => 'Larissa',
                        'EDIVALDO' => 'Edivaldo',
                        'OPERACAO' => 'Operação',
                        'SERASA' => 'SERASA',
                    ]),
            ])
            ->recordActions([
                Action::make('concluir')
                    ->label('Concluir')
                    ->icon(Heroicon::Check)
                    ->color('success')
                    ->authorize(fn (PopFinanceiroChecklist $record): bool => auth()->user()?->can('update', $record) ?? false)
                    ->visible(fn (PopFinanceiroChecklist $record): bool => $record->status !== PopChecklistStatus::Concluido)
                    ->action(fn (PopFinanceiroChecklist $record): bool => $record->update([
                        'status' => PopChecklistStatus::Concluido,
                        'concluido_em' => now(),
                    ])),
                Action::make('reabrir')
                    ->label('Reabrir')
                    ->icon(Heroicon::ArrowPath)
                    ->color('gray')
                    ->authorize(fn (PopFinanceiroChecklist $record): bool => auth()->user()?->can('update', $record) ?? false)
                    ->visible(fn (PopFinanceiroChecklist $record): bool => $record->status !== PopChecklistStatus::Pendente)
                    ->action(fn (PopFinanceiroChecklist $record): bool => $record->update([
                        'status' => PopChecklistStatus::Pendente,
                        'concluido_em' => null,
                    ])),
                Action::make('cancelar')
                    ->label('Cancelar')
                    ->icon(Heroicon::XMark)
                    ->color('danger')
                    ->requiresConfirmation()
                    ->authorize(fn (PopFinanceiroChecklist $record): bool => auth()->user()?->can('update', $record) ?? false)
                    ->visible(fn (PopFinanceiroChecklist $record): bool => $record->status !== PopChecklistStatus::Cancelado)
                    ->action(fn (PopFinanceiroChecklist $record): bool => $record->update([
                        'status' => PopChecklistStatus::Cancelado,
                    ])),
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
