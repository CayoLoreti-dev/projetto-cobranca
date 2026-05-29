<?php

namespace App\Filament\Resources\SerasaOcorrencias\Pages;

use App\Filament\Resources\SerasaOcorrencias\SerasaOcorrenciaResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSerasaOcorrencia extends ViewRecord
{
    protected static string $resource = SerasaOcorrenciaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}