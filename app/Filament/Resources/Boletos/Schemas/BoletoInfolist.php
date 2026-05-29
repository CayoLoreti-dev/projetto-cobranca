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
                TextEntry::make('cobranca.cliente.nome')
                    ->label('Cliente')
                    ->placeholder('-'),
                TextEntry::make('cobranca.codigo')
                    ->label('Cobrança')
                    ->placeholder('-'),
                TextEntry::make('parcela.numero')
                    ->label('Parcela')
                    ->placeholder('-'),
                TextEntry::make('linha_digitavel')
                    ->label('Linha digitável')
                    ->placeholder('-'),
                TextEntry::make('codigo_barras')
                    ->label('Código de barras')
                    ->placeholder('-'),
                TextEntry::make('valor')
                    ->label('Valor')
                    ->money('BRL'),
                TextEntry::make('vencimento')
                    ->label('Vencimento')
                    ->date(),
                TextEntry::make('status')
                    ->label('Status')
                    ->badge(),
                TextEntry::make('pdf_original_name')
                    ->label('PDF anexado')
                    ->placeholder('-'),
                TextEntry::make('pdf_path')
                    ->label('Caminho privado')
                    ->placeholder('-'),
                TextEntry::make('pdf_url')
                    ->label('URL externa')
                    ->placeholder('-'),
                TextEntry::make('gerado_em')
                    ->label('Gerado em')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('enviado_em')
                    ->label('Enviado em')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('lido_em')
                    ->label('Lido em')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('recebido_em')
                    ->label('Recebido em')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('pago_em')
                    ->label('Pago em')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('observacoes')
                    ->label('Observações')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('id')
                    ->label('ID'),
                TextEntry::make('created_at')
                    ->label('Criado em')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->label('Atualizado em')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->label('Excluído em')
                    ->dateTime()
                    ->visible(fn (Boleto $record): bool => $record->trashed()),
            ]);
    }
}
