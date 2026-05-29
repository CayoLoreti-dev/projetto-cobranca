<?php

namespace App\Filament\Resources\SerasaOcorrencias\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SerasaOcorrenciaInfolist
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
                TextEntry::make('documento_devedor')
                    ->label('Documento do devedor')
                    ->placeholder('-'),
                TextEntry::make('protocolo')
                    ->label('Protocolo / remessa')
                    ->placeholder('-'),
                TextEntry::make('responsavel.name')
                    ->label('Responsável')
                    ->placeholder('-'),
                TextEntry::make('etapa')
                    ->label('Etapa SERASA')
                    ->badge(),
                TextEntry::make('status')
                    ->label('Status')
                    ->badge(),
                TextEntry::make('valor_negativado')
                    ->label('Valor informado ao SERASA')
                    ->money('BRL')
                    ->placeholder('-'),
                TextEntry::make('data_limite_regularizacao')
                    ->label('Limite para regularização')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('agendado_para')
                    ->label('Agendado para')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('executado_em')
                    ->label('Executado em')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('data_baixa')
                    ->label('Baixa em')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('motivo_baixa')
                    ->label('Motivo da baixa')
                    ->placeholder('-'),
                TextEntry::make('observacoes')
                    ->label('Observações')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('metadata')
                    ->label('Metadados')
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
            ]);
    }
}
