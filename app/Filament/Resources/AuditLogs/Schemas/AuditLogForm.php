<?php

namespace App\Filament\Resources\AuditLogs\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class AuditLogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->label('Usuário')
                    ->numeric(),
                TextInput::make('auditable_type')
                    ->label('Tipo do registro'),
                TextInput::make('auditable_id')
                    ->label('Registro'),
                TextInput::make('action')
                    ->label('Ação')
                    ->required(),
                TextInput::make('before')
                    ->label('Antes'),
                TextInput::make('after')
                    ->label('Depois'),
                Textarea::make('reason')
                    ->label('Motivo')
                    ->columnSpanFull(),
                TextInput::make('ip_address')
                    ->label('Endereço IP'),
                Textarea::make('user_agent')
                    ->label('User agent')
                    ->columnSpanFull(),
                TextInput::make('origin')
                    ->label('Origem')
                    ->required()
                    ->default('system'),
                TextInput::make('checksum_sha256')
                    ->label('Checksum SHA-256'),
                DateTimePicker::make('occurred_at')
                    ->label('Ocorreu em')
                    ->required(),
            ]);
    }
}
