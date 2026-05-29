<?php

namespace App\Filament\Resources\BoletoDdaControles\Pages;

use App\Filament\Resources\BoletoDdaControles\BoletoDdaControleResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditBoletoDdaControle extends EditRecord
{
    protected static string $resource = BoletoDdaControleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}