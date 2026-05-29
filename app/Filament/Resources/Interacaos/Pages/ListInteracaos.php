<?php

namespace App\Filament\Resources\Interacaos\Pages;

use App\Filament\Resources\Interacaos\InteracaoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListInteracaos extends ListRecords
{
    protected static string $resource = InteracaoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
