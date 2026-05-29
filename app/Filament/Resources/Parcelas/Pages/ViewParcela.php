<?php

namespace App\Filament\Resources\Parcelas\Pages;

use App\Filament\Resources\Parcelas\ParcelaResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewParcela extends ViewRecord
{
    protected static string $resource = ParcelaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
