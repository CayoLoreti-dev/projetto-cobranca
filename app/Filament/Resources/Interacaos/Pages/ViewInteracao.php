<?php

namespace App\Filament\Resources\Interacaos\Pages;

use App\Filament\Resources\Interacaos\InteracaoResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewInteracao extends ViewRecord
{
    protected static string $resource = InteracaoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
