<?php

namespace App\Filament\Resources\Parcelas\Schemas;

use App\Models\Parcela;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ParcelaInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id')
                    ->label('ID'),
                TextEntry::make('cobranca_id'),
                TextEntry::make('numero')
                    ->numeric(),
                TextEntry::make('valor')
                    ->numeric(),
                TextEntry::make('vencimento')
                    ->date(),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('paga_em')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('valor_pago')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('forma_pagamento')
                    ->placeholder('-'),
                TextEntry::make('observacoes')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (Parcela $record): bool => $record->trashed()),
            ]);
    }
}
