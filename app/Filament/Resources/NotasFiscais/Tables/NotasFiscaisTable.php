<?php

namespace App\Filament\Resources\NotasFiscais\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class NotasFiscaisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID'),
                TextColumn::make('cobranca.codigo')->label('Cobrança')->searchable(),
                TextColumn::make('boleto.linha_digitavel')->label('Boleto')->searchable(),
                TextColumn::make('numero')->label('Número')->searchable(),
                TextColumn::make('serie')->label('Série')->searchable(),
                TextColumn::make('status')->label('Status')->badge()->searchable(),
                TextColumn::make('valor')->label('Valor')->numeric()->sortable(),
                TextColumn::make('emitida_em')->label('Emitida em')->dateTime()->sortable(),
                TextColumn::make('competencia')->label('Competência')->date()->sortable(),
                TextColumn::make('created_at')->label('Criado em')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')->label('Atualizado em')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')->label('Excluído em')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
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