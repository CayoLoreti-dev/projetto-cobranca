<?php

namespace App\Filament\Resources\Clientes\Schemas;

use App\Enums\ClienteStatus;
use App\Enums\ClienteTipo;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ClienteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nome')
                    ->label('Nome')
                    ->required(),
                Select::make('tipo')
                    ->label('Tipo')
                    ->options(ClienteTipo::class)
                    ->required(),
                TextInput::make('documento')
                    ->label('Documento')
                    ->required(),
                TextInput::make('responsavel_financeiro')
                    ->label('Responsável financeiro')
                    ->required(),
                TextInput::make('email')
                    ->label('E-mail')
                    ->email()
                    ->required(),
                TextInput::make('telefone')
                    ->label('Telefone')
                    ->tel()
                    ->required(),
                TextInput::make('whatsapp')
                    ->label('WhatsApp'),
                Textarea::make('endereco')
                    ->label('Endereço')
                    ->columnSpanFull(),
                Select::make('status')
                    ->label('Status')
                    ->options(ClienteStatus::class)
                    ->default('ATIVO')
                    ->required(),
                Textarea::make('observacoes')
                    ->label('Observações')
                    ->columnSpanFull(),
                DateTimePicker::make('archived_at')
                    ->label('Arquivado em'),
            ]);
    }
}
