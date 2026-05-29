<?php

namespace App\Filament\Resources\Boletos\Pages;

use App\Filament\Resources\Boletos\BoletoResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditBoleto extends EditRecord
{
    protected static string $resource = BoletoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
