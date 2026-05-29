<?php

namespace App\Filament\Resources\Tarefas\Schemas;

use App\Models\Tarefa;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class TarefaInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id')
                    ->label('ID'),
                TextEntry::make('cliente_id')
                    ->placeholder('-'),
                TextEntry::make('cobranca_id')
                    ->placeholder('-'),
                TextEntry::make('assigned_to_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('tipo'),
                TextEntry::make('titulo'),
                TextEntry::make('descricao')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('prioridade')
                    ->numeric(),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('vence_em')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('iniciada_em')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('concluida_em')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (Tarefa $record): bool => $record->trashed()),
            ]);
    }
}
