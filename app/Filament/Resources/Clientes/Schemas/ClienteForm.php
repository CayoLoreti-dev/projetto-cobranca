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
                    ->required()
                    ->maxLength(255),
                Select::make('tipo')
                    ->label('Tipo')
                    ->options(ClienteTipo::class)
                    ->required(),
                TextInput::make('documento')
                    ->label('Documento')
                    ->required()
                    ->maxLength(32),
                TextInput::make('responsavel_financeiro')
                    ->label('Responsável financeiro')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('E-mail')
                    ->email()
                    ->required()
                    ->maxLength(255),
                TextInput::make('telefone')
                    ->label('Telefone')
                    ->tel()
                    ->required()
                    ->maxLength(32),
                TextInput::make('whatsapp')
                    ->label('WhatsApp')
                    ->maxLength(32),
                Textarea::make('endereco')
                    ->label('Endereço')
                    ->columnSpanFull()
                    ->maxLength(1000),
                Select::make('status')
                    ->label('Status')
                    ->options(ClienteStatus::class)
                    ->default('ATIVO')
                    ->required(),
                Textarea::make('observacoes')
                    ->label('Observações')
                    ->columnSpanFull()
                    ->maxLength(2000),
                DateTimePicker::make('archived_at')
                    ->label('Arquivado em'),
            ]);
    }
}
