<?php

namespace App\Filament\Resources\SerasaOcorrencias\Schemas;

use App\Models\SerasaOcorrencia;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SerasaOcorrenciaInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id')->label('ID'),
                TextEntry::make('cobranca.codigo')->label('Cobrança')->placeholder('-'),
                TextEntry::make('responsavel.name')->label('Responsável')->placeholder('-'),
                TextEntry::make('etapa')->label('Etapa')->badge(),
                TextEntry::make('status')->label('Status')->badge(),
                TextEntry::make('agendado_para')->label('Agendado para')->dateTime()->placeholder('-'),
                TextEntry::make('executado_em')->label('Executado em')->dateTime()->placeholder('-'),
                TextEntry::make('observacoes')->label('Observações')->placeholder('-')->columnSpanFull(),
                TextEntry::make('metadata')->label('Metadados')->placeholder('-')->columnSpanFull(),
                TextEntry::make('created_at')->label('Criado em')->dateTime()->placeholder('-'),
                TextEntry::make('updated_at')->label('Atualizado em')->dateTime()->placeholder('-'),
            ]);
    }
}