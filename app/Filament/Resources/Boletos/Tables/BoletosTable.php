<?php

namespace App\Filament\Resources\Boletos\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class BoletosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID'),
                TextColumn::make('parcela_id'),
                TextColumn::make('cobranca_id'),
                TextColumn::make('pdf_file_id'),
                TextColumn::make('linha_digitavel')
                    ->searchable(),
                TextColumn::make('codigo_barras')
                    ->searchable(),
                TextColumn::make('valor')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('vencimento')
                    ->date()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->searchable(),
                TextColumn::make('gerado_em')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('enviado_em')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('lido_em')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('recebido_em')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('pago_em')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('pdf_url')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
