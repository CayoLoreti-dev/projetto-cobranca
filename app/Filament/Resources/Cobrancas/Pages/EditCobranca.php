<?php

namespace App\Filament\Resources\Cobrancas\Pages;

use App\Filament\Resources\Cobrancas\CobrancaResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditCobranca extends EditRecord
{
    protected static string $resource = CobrancaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
