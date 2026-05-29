<?php

namespace App\Filament\Resources\Cobrancas\Schemas;

use App\Models\Cobranca;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CobrancaInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id')
                    ->label('ID'),
                TextEntry::make('cliente.nome')
                    ->label('Cliente')
                    ->placeholder('-'),
                TextEntry::make('codigo')
                    ->label('Código'),
                TextEntry::make('categoria')
                    ->label('Categoria')
                    ->placeholder('-'),
                TextEntry::make('tipo')
                    ->label('Tipo')
                    ->badge(),
                TextEntry::make('valor_total')
                    ->label('Valor total')
                    ->numeric(),
                TextEntry::make('moeda')
                    ->label('Moeda'),
                TextEntry::make('status')
                    ->label('Status')
                    ->badge(),
                TextEntry::make('data_emissao')
                    ->label('Data de emissão')
                    ->date(),
                TextEntry::make('data_vencimento_principal')
                    ->label('Vencimento principal')
                    ->date(),
                TextEntry::make('responsavel.name')
                    ->label('Responsável')
                    ->placeholder('-'),
                TextEntry::make('prioridade')
                    ->label('Prioridade')
                    ->numeric(),
                TextEntry::make('proxima_acao')
                    ->label('Próxima ação')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('data_proxima_acao')
                    ->label('Próxima ação em')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('observacoes')
                    ->label('Observações')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('createdBy.name')
                    ->label('Criado por')
                    ->placeholder('-'),
                TextEntry::make('updatedBy.name')
                    ->label('Atualizado por')
                    ->placeholder('-'),
                TextEntry::make('archived_at')
                    ->label('Arquivado em')
                    ->dateTime()
                    ->placeholder('-'),
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
                    ->visible(fn (Cobranca $record): bool => $record->trashed()),
            ]);
    }
}
