<?php

namespace App\Filament\Resources\BoletoDdaControles\Schemas;

use App\Models\BoletoDdaControle;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class BoletoDdaControleInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id')->label('ID'),
                TextEntry::make('boleto.linha_digitavel')->label('Boleto')->placeholder('-'),
                TextEntry::make('status')->label('Status')->badge(),
                TextEntry::make('apareceu_no_dda')->label('Apareceu no DDA')->placeholder('-'),
                TextEntry::make('verificado_em')->label('Verificado em')->dateTime()->placeholder('-'),
                TextEntry::make('ultimo_retorno')->label('Último retorno')->placeholder('-')->columnSpanFull(),
                TextEntry::make('created_at')->label('Criado em')->dateTime()->placeholder('-'),
                TextEntry::make('updated_at')->label('Atualizado em')->dateTime()->placeholder('-'),
            ]);
    }
}