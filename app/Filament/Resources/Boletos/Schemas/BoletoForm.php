<?php

namespace App\Filament\Resources\Boletos\Schemas;

use App\Enums\BoletoStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class BoletoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('parcela_id')
                    ->rules(['nullable', 'exists:parcelas,id']),
                TextInput::make('cobranca_id')
                    ->rules(['nullable', 'exists:cobrancas,id']),
                TextInput::make('pdf_file_id')
                    ->rules(['nullable', 'exists:arquivos,id']),
                TextInput::make('linha_digitavel')
                    ->maxLength(255),
                TextInput::make('codigo_barras')
                    ->maxLength(255),
                TextInput::make('valor')
                    ->required()
                    ->numeric(),
                DatePicker::make('vencimento')
                    ->required(),
                Select::make('status')
                    ->options(BoletoStatus::class)
                    ->default('EMITIDO')
                    ->required(),
                DateTimePicker::make('gerado_em'),
                DateTimePicker::make('enviado_em'),
                DateTimePicker::make('lido_em'),
                DateTimePicker::make('recebido_em'),
                DateTimePicker::make('pago_em'),
                TextInput::make('pdf_url')
                    ->url(),
                Textarea::make('observacoes')
                    ->columnSpanFull()
                    ->maxLength(2000),
                TextInput::make('metadata')
                    ->rules(['nullable', 'json']),
            ]);
    }
}
