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
                TextInput::make('parcela_id'),
                TextInput::make('cobranca_id'),
                TextInput::make('pdf_file_id'),
                TextInput::make('linha_digitavel'),
                TextInput::make('codigo_barras'),
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
                    ->columnSpanFull(),
                TextInput::make('metadata'),
            ]);
    }
}
