<?php

namespace App\Filament\Resources\NotasFiscais\Schemas;

use App\Enums\NotaFiscalStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class NotaFiscalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('cobranca_id')
                    ->label('Cobrança')
                    ->relationship('cobranca', 'codigo')
                    ->searchable()
                    ->required()
                    ->rules(['required', 'exists:cobrancas,id']),
                Select::make('boleto_id')
                    ->label('Boleto')
                    ->relationship('boleto', 'linha_digitavel')
                    ->searchable()
                    ->placeholder('Opcional')
                    ->rules(['nullable', 'exists:boletos,id']),
                TextInput::make('numero')
                    ->label('Número')
                    ->maxLength(255),
                TextInput::make('serie')
                    ->label('Série')
                    ->maxLength(255),
                Select::make('status')
                    ->label('Status')
                    ->options(NotaFiscalStatus::class)
                    ->default('PENDENTE_EMISSAO')
                    ->required(),
                TextInput::make('valor')
                    ->label('Valor')
                    ->required()
                    ->numeric(),
                DateTimePicker::make('emitida_em')
                    ->label('Emitida em'),
                DatePicker::make('competencia')
                    ->label('Competência'),
                Textarea::make('observacoes')
                    ->label('Observações')
                    ->columnSpanFull()
                    ->maxLength(2000),
                TextInput::make('metadata')
                    ->label('Metadados')
                    ->rules(['nullable', 'json']),
            ]);
    }
}