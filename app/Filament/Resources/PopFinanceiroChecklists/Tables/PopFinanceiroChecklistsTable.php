<?php

namespace App\Filament\Resources\PopFinanceiroChecklists\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
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
                TextColumn::make('reference_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('etapa')
                    ->badge()
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->searchable(),
                TextColumn::make('escalonamento_nivel')
                    ->searchable()
                    ->placeholder('-'),
                TextColumn::make('acao_canal')
                    ->searchable()
                    ->placeholder('-'),
                TextColumn::make('titulo')
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
                    ->numeric(),
                TextColumn::make('responsavel.name')
                    ->label('Responsável')
                    ->searchable()
                    ->placeholder('-'),
                TextColumn::make('sla_limite_em')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('-'),
                TextColumn::make('concluido_em')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('-'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'PENDENTE' => 'Pendente',
                        'CONCLUIDO' => 'Concluído',
                        'CANCELADO' => 'Cancelado',
                    ]),
                SelectFilter::make('escalonamento_nivel')
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
