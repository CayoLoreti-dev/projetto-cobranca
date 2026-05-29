<?php

namespace App\Filament\Resources\Parcelas\Pages;

use App\Filament\Resources\Parcelas\ParcelaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListParcelas extends ListRecords
{
    protected static string $resource = ParcelaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
