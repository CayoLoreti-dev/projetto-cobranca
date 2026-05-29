<?php

namespace App\Filament\Resources\Interacaos\Pages;

use App\Filament\Resources\Interacaos\InteracaoResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditInteracao extends EditRecord
{
    protected static string $resource = InteracaoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
