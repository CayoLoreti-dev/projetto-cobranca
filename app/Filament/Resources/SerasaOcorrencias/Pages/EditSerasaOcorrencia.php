<?php

namespace App\Filament\Resources\SerasaOcorrencias\Pages;

use App\Filament\Resources\SerasaOcorrencias\SerasaOcorrenciaResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditSerasaOcorrencia extends EditRecord
{
    protected static string $resource = SerasaOcorrenciaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}