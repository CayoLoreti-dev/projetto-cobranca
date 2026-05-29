<?php

namespace App\Filament\Resources\Boletos\Schemas;

use App\Models\Boleto;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class BoletoInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id')
                    ->label('ID'),
                TextEntry::make('parcela_id')
                    ->placeholder('-'),
                TextEntry::make('cobranca_id')
                    ->placeholder('-'),
                TextEntry::make('pdf_file_id')
                    ->placeholder('-'),
                TextEntry::make('linha_digitavel')
                    ->placeholder('-'),
                TextEntry::make('codigo_barras')
                    ->placeholder('-'),
                TextEntry::make('valor')
                    ->numeric(),
                TextEntry::make('vencimento')
                    ->date(),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('gerado_em')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('enviado_em')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('lido_em')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('recebido_em')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('pago_em')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('pdf_url')
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
                    ->visible(fn (Boleto $record): bool => $record->trashed()),
            ]);
    }
}
