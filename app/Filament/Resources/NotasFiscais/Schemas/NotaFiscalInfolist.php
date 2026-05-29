<?php

namespace App\Filament\Resources\NotasFiscais\Schemas;

use App\Models\NotaFiscal;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class NotaFiscalInfolist
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
                TextEntry::make('boleto.linha_digitavel')
                    ->label('Boleto')
                    ->placeholder('-'),
                TextEntry::make('numero')
                    ->label('Número')
                    ->placeholder('-'),
                TextEntry::make('serie')
                    ->label('Série')
                    ->placeholder('-'),
                TextEntry::make('status')
                    ->label('Status')
                    ->badge(),
                TextEntry::make('valor')
                    ->label('Valor')
                    ->money('BRL'),
                TextEntry::make('emitida_em')
                    ->label('Emitida em')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('competencia')
                    ->label('Competência')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('pdf_original_name')
                    ->label('PDF anexado')
                    ->placeholder('-'),
                TextEntry::make('pdf_path')
                    ->label('Caminho privado')
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
                    ->visible(fn (NotaFiscal $record): bool => $record->trashed()),
            ]);
    }
}
