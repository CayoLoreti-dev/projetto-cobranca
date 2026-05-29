<?php

namespace App\Filament\Resources\AuditLogs\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class AuditLogInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id')
                    ->label('ID'),
                TextEntry::make('user_id')
                    ->label('Usuário')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('auditable_type')
                    ->label('Tipo do registro')
                    ->placeholder('-'),
                TextEntry::make('auditable_id')
                    ->label('Registro')
                    ->placeholder('-'),
                TextEntry::make('action')
                    ->label('Ação'),
                TextEntry::make('reason')
                    ->label('Motivo')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('ip_address')
                    ->label('Endereço IP')
                    ->placeholder('-'),
                TextEntry::make('user_agent')
                    ->label('User agent')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('origin')
                    ->label('Origem'),
                TextEntry::make('checksum_sha256')
                    ->label('Checksum SHA-256')
                    ->placeholder('-'),
                TextEntry::make('occurred_at')
                    ->label('Ocorreu em')
                    ->dateTime(),
            ]);
    }
}
