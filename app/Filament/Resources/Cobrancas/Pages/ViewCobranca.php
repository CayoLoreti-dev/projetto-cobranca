<?php

namespace App\Filament\Resources\Cobrancas\Pages;

use App\Filament\Resources\Cobrancas\CobrancaResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCobranca extends ViewRecord
{
    protected static string $resource = CobrancaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
