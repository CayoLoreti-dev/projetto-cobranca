<?php

namespace App\Filament\Resources\Parcelas\Schemas;

use App\Enums\ParcelaStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Schema;

class ParcelaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('cobranca_id')
                    ->required(),
                TextInput::make('numero')
                    ->required()
                    ->numeric(),
                TextInput::make('valor')
                    ->required()
                    ->numeric(),
                DatePicker::make('vencimento')
                    ->required(),
                Select::make('status')
                    ->options(ParcelaStatus::class)
                    ->default('PENDENTE')
                    ->live()
                    ->afterStateUpdated(function (Set $set, $state): void {
                        if ($state !== 'PAGA') {
                            $set('paga_em', null);
                            $set('valor_pago', null);
                            $set('forma_pagamento', null);
                        }
                    })
                    ->required(),
                DateTimePicker::make('paga_em')
                    ->label('Paga em')
                    ->hidden(fn (Get $get): bool => $get('status') !== 'PAGA')
                    ->dehydrated(fn (Get $get): bool => $get('status') === 'PAGA'),
                TextInput::make('valor_pago')
                    ->numeric()
                    ->hidden(fn (Get $get): bool => $get('status') !== 'PAGA')
                    ->dehydrated(fn (Get $get): bool => $get('status') === 'PAGA'),
                Select::make('forma_pagamento')
                    ->label('Forma de pagamento')
                    ->options([
                        'BOLETO' => 'Boleto',
                        'PIX' => 'Pix',
                        'CARTAO' => 'Cartão',
                        'DINHEIRO' => 'Dinheiro',
                    ])
                    ->hidden(fn (Get $get): bool => $get('status') !== 'PAGA')
                    ->dehydrated(fn (Get $get): bool => $get('status') === 'PAGA'),
                Textarea::make('observacoes')
                    ->columnSpanFull(),
                TextInput::make('metadata'),
            ]);
    }
}
