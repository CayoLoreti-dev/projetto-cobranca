<?php

namespace App\Filament\Resources\Boletos\Pages;

use App\Filament\Resources\Boletos\BoletoResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewBoleto extends ViewRecord
{
    protected static string $resource = BoletoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
