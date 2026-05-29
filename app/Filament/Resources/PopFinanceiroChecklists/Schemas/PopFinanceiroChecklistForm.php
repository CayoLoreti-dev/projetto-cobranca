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
                TextInput::make('cliente_id'),
                TextInput::make('cobranca_id'),
                TextInput::make('parcela_id'),
                TextInput::make('boleto_id'),
                TextInput::make('assigned_to_id')
                    ->numeric(),
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
                    ->required(),
                Textarea::make('descricao')
                    ->columnSpanFull(),
                DateTimePicker::make('sla_limite_em'),
                DateTimePicker::make('concluido_em'),
                Textarea::make('metadata')
                    ->columnSpanFull(),
            ]);
    }
}
