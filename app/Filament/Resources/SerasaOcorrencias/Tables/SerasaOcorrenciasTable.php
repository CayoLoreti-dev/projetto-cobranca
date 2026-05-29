<?php

namespace App\Filament\Resources\SerasaOcorrencias\Tables;

use App\Enums\SerasaEtapa;
use App\Enums\SerasaStatus;
use App\Models\SerasaOcorrencia;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SerasaOcorrenciasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('agendado_para')
            ->columns([
                TextColumn::make('cobranca.cliente.nome')
                    ->label('Cliente')
                    ->searchable(),
                TextColumn::make('cobranca.codigo')
                    ->label('Cobrança')
                    ->searchable(),
                TextColumn::make('documento_devedor')
                    ->label('Documento')
                    ->searchable()
                    ->placeholder('-'),
                TextColumn::make('protocolo')
                    ->label('Protocolo')
                    ->searchable()
                    ->placeholder('-'),
                TextColumn::make('responsavel.name')
                    ->label('Responsável')
                    ->searchable()
                    ->placeholder('-'),
                TextColumn::make('etapa')
                    ->label('Etapa')
                    ->badge()
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->searchable(),
                TextColumn::make('valor_negativado')
                    ->label('Valor SERASA')
                    ->money('BRL')
                    ->sortable()
                    ->placeholder('-'),
                TextColumn::make('data_limite_regularizacao')
                    ->label('Regularizar até')
                    ->date()
                    ->sortable()
                    ->placeholder('-'),
                TextColumn::make('agendado_para')
                    ->label('Agendado para')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('-'),
                TextColumn::make('executado_em')
                    ->label('Executado em')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('-'),
                TextColumn::make('data_baixa')
                    ->label('Baixa em')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('-'),
                TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Atualizado em')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('etapa')
                    ->options(SerasaEtapa::class),
                SelectFilter::make('status')
                    ->options(SerasaStatus::class),
            ])
            ->recordActions([
                Action::make('executar')
                    ->label('Executar')
                    ->icon(Heroicon::Check)
                    ->color('success')
                    ->requiresConfirmation()
                    ->authorize(fn (SerasaOcorrencia $record): bool => auth()->user()?->can('update', $record) ?? false)
                    ->visible(fn (SerasaOcorrencia $record): bool => $record->status !== SerasaStatus::Executado)
                    ->action(fn (SerasaOcorrencia $record): bool => $record->update([
                        'status' => SerasaStatus::Executado,
                        'executado_em' => now(),
                    ])),
                Action::make('baixar')
                    ->label('Baixar')
                    ->icon(Heroicon::ArrowPath)
                    ->color('gray')
                    ->requiresConfirmation()
                    ->authorize(fn (SerasaOcorrencia $record): bool => auth()->user()?->can('update', $record) ?? false)
                    ->visible(fn (SerasaOcorrencia $record): bool => blank($record->data_baixa))
                    ->action(fn (SerasaOcorrencia $record): bool => $record->update([
                        'status' => SerasaStatus::Executado,
                        'data_baixa' => now(),
                        'motivo_baixa' => $record->motivo_baixa ?: 'Baixa manual pelo painel',
                    ])),
                Action::make('cancelar')
                    ->label('Cancelar')
                    ->icon(Heroicon::XMark)
                    ->color('danger')
                    ->requiresConfirmation()
                    ->authorize(fn (SerasaOcorrencia $record): bool => auth()->user()?->can('update', $record) ?? false)
                    ->visible(fn (SerasaOcorrencia $record): bool => $record->status !== SerasaStatus::Cancelado)
                    ->action(fn (SerasaOcorrencia $record): bool => $record->update([
                        'status' => SerasaStatus::Cancelado,
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
