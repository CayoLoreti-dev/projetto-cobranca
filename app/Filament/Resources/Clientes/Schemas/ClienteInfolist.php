<?php

namespace App\Filament\Resources\Clientes\Schemas;

use App\Models\Cliente;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ClienteInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id')
                    ->label('ID'),
                TextEntry::make('nome')
                    ->label('Nome'),
                TextEntry::make('tipo')
                    ->label('Tipo')
                    ->badge(),
                TextEntry::make('documento')
                    ->label('Documento'),
                TextEntry::make('responsavel_financeiro')
                    ->label('Responsável financeiro'),
                TextEntry::make('email')
                    ->label('E-mail'),
                TextEntry::make('telefone')
                    ->label('Telefone'),
                TextEntry::make('whatsapp')
                    ->label('WhatsApp')
                    ->placeholder('-'),
                TextEntry::make('endereco')
                    ->label('Endereço')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('status')
                    ->label('Status')
                    ->badge(),
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
                    ->dateTime()
                    ->visible(fn (Cliente $record): bool => $record->trashed()),
            ]);
    }
}
