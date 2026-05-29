<?php

namespace App\Filament\Resources\SerasaOcorrencias\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SerasaOcorrenciasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID'),
                TextColumn::make('cobranca.codigo')->label('Cobrança')->searchable(),
                TextColumn::make('responsavel.name')->label('Responsável')->searchable(),
                TextColumn::make('etapa')->label('Etapa')->badge()->searchable(),
                TextColumn::make('status')->label('Status')->badge()->searchable(),
                TextColumn::make('agendado_para')->label('Agendado para')->dateTime()->sortable(),
                TextColumn::make('executado_em')->label('Executado em')->dateTime()->sortable(),
                TextColumn::make('created_at')->label('Criado em')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')->label('Atualizado em')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
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