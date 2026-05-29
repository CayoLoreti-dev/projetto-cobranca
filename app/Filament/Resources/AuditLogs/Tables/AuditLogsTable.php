<?php

namespace App\Filament\Resources\AuditLogs\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AuditLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID'),
                TextColumn::make('user_id')
                    ->label('Usuário')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('auditable_type')
                    ->label('Tipo do registro')
                    ->searchable(),
                TextColumn::make('auditable_id')
                    ->label('Registro')
                    ->searchable(),
                TextColumn::make('action')
                    ->label('Ação')
                    ->searchable(),
                TextColumn::make('ip_address')
                    ->label('Endereço IP')
                    ->searchable(),
                TextColumn::make('origin')
                    ->label('Origem')
                    ->searchable(),
                TextColumn::make('checksum_sha256')
                    ->label('Checksum SHA-256')
                    ->searchable(),
                TextColumn::make('occurred_at')
                    ->label('Ocorreu em')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([]);
    }
}
