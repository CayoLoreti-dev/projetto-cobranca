<?php

namespace App\Filament\Resources\PopFinanceiroChecklists\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PopFinanceiroChecklistInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id')
                    ->label('ID'),
                TextEntry::make('reference_date')
                    ->date(),
                TextEntry::make('cliente_id'),
                TextEntry::make('cobranca_id'),
                TextEntry::make('parcela_id'),
                TextEntry::make('boleto_id'),
                TextEntry::make('assigned_to_id')
                    ->numeric(),
                TextEntry::make('etapa')
                    ->badge(),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('acao_canal')
                    ->placeholder('-'),
                TextEntry::make('escalonamento_nivel')
                    ->placeholder('-'),
                TextEntry::make('titulo')
                    ->columnSpanFull(),
                TextEntry::make('descricao')
                    ->columnSpanFull(),
                TextEntry::make('sla_limite_em')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('concluido_em')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
