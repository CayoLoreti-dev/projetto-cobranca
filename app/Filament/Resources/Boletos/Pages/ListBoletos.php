<?php

namespace App\Filament\Resources\Boletos\Pages;

use App\Filament\Resources\Boletos\BoletoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBoletos extends ListRecords
{
    protected static string $resource = BoletoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
