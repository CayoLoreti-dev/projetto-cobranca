<?php

namespace App\Filament\Resources\BoletoDdaControles\Schemas;

use App\Enums\DdaStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class BoletoDdaControleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('boleto_id')
                    ->label('Boleto')
                    ->relationship('boleto', 'linha_digitavel')
                    ->searchable()
                    ->required(),
                Select::make('status')
                    ->label('Status')
                    ->options(DdaStatus::class)
                    ->default('PENDENTE_VERIFICACAO')
                    ->required(),
                Select::make('apareceu_no_dda')
                    ->label('Apareceu no DDA')
                    ->options([
                        1 => 'Sim',
                        0 => 'Não',
                    ])
                    ->placeholder('Sem verificação'),
                DateTimePicker::make('verificado_em')
                    ->label('Verificado em'),
                Textarea::make('ultimo_retorno')
                    ->label('Último retorno')
                    ->columnSpanFull()
                    ->maxLength(2000),
                TextInput::make('metadata')
                    ->label('Metadados')
                    ->rules(['nullable', 'json']),
            ]);
    }
}