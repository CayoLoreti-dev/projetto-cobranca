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
                TextEntry::make('reference_date')
                    ->label('Data de referência')
                    ->date(),
                TextEntry::make('cliente.nome')
                    ->label('Cliente')
                    ->placeholder('-'),
                TextEntry::make('cobranca.codigo')
                    ->label('Cobrança')
                    ->placeholder('-'),
                TextEntry::make('parcela.numero')
                    ->label('Parcela')
                    ->placeholder('-'),
                TextEntry::make('boleto.linha_digitavel')
                    ->label('Boleto')
                    ->placeholder('-'),
                TextEntry::make('responsavel.name')
                    ->label('Responsável')
                    ->placeholder('-'),
                TextEntry::make('etapa')
                    ->label('Etapa POP')
                    ->badge(),
                TextEntry::make('status')
                    ->label('Status')
                    ->badge(),
                TextEntry::make('acao_canal')
                    ->label('Canal')
                    ->placeholder('-'),
                TextEntry::make('escalonamento_nivel')
                    ->label('Escalonamento')
                    ->placeholder('-'),
                TextEntry::make('titulo')
                    ->label('Título')
                    ->columnSpanFull(),
                TextEntry::make('descricao')
                    ->label('Descrição')
                    ->columnSpanFull()
                    ->placeholder('-'),
                TextEntry::make('sla_limite_em')
                    ->label('SLA limite')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('concluido_em')
                    ->label('Concluído em')
                    ->dateTime()
                    ->placeholder('-'),
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
            ]);
    }
}
