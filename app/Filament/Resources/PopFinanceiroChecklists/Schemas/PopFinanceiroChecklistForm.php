<?php

namespace App\Filament\Resources\PopFinanceiroChecklists\Schemas;

use App\Enums\PopChecklistStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class PopFinanceiroChecklistForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('reference_date')
                    ->required(),
                TextInput::make('cliente_id')
                    ->rules(['nullable', 'exists:clientes,id']),
                TextInput::make('cobranca_id')
                    ->rules(['nullable', 'exists:cobrancas,id']),
                TextInput::make('parcela_id')
                    ->rules(['nullable', 'exists:parcelas,id']),
                TextInput::make('boleto_id')
                    ->rules(['nullable', 'exists:boletos,id']),
                TextInput::make('assigned_to_id')
                    ->numeric()
                    ->rules(['nullable', 'exists:users,id']),
                TextInput::make('etapa')
                    ->required(),
                Select::make('status')
                    ->options(PopChecklistStatus::class)
                    ->required(),
                TextInput::make('acao_canal')
                    ->placeholder('SISTEMA / EMAIL / WHATSAPP'),
                TextInput::make('escalonamento_nivel')
                    ->placeholder('VENDEDOR / LARISSA / EDIVALDO / SERASA'),
                TextInput::make('titulo')
                    ->required()
                    ->maxLength(255),
                Textarea::make('descricao')
                    ->columnSpanFull()
                    ->maxLength(2000),
                DateTimePicker::make('sla_limite_em'),
                DateTimePicker::make('concluido_em'),
                Textarea::make('metadata')
                    ->columnSpanFull()
                    ->maxLength(2000)
                    ->helperText('JSON string allowed')
                    ->rules(['nullable', 'json']),
            ]);
    }
}
