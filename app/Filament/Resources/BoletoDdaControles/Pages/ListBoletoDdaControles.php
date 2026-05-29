<?php

namespace App\Filament\Resources\BoletoDdaControles\Pages;

use App\Filament\Resources\BoletoDdaControles\BoletoDdaControleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBoletoDdaControles extends ListRecords
{
    protected static string $resource = BoletoDdaControleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}