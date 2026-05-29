<?php

namespace App\Filament\Resources\Cobrancas\Pages;

use App\Filament\Resources\Cobrancas\CobrancaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCobrancas extends ListRecords
{
    protected static string $resource = CobrancaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
