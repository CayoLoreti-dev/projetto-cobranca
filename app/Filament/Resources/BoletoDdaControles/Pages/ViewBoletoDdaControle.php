<?php

namespace App\Filament\Resources\BoletoDdaControles\Pages;

use App\Filament\Resources\BoletoDdaControles\BoletoDdaControleResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewBoletoDdaControle extends ViewRecord
{
    protected static string $resource = BoletoDdaControleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}