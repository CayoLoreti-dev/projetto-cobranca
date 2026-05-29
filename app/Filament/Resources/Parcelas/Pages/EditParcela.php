<?php

namespace App\Filament\Resources\Parcelas\Pages;

use App\Filament\Resources\Parcelas\ParcelaResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditParcela extends EditRecord
{
    protected static string $resource = ParcelaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
