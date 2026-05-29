<?php

namespace App\Filament\Resources\SerasaOcorrencias\Pages;

use App\Filament\Resources\SerasaOcorrencias\SerasaOcorrenciaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSerasaOcorrencias extends ListRecords
{
    protected static string $resource = SerasaOcorrenciaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}