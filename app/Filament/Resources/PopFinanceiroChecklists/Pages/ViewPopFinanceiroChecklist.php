<?php

namespace App\Filament\Resources\PopFinanceiroChecklists\Pages;

use App\Filament\Resources\PopFinanceiroChecklists\PopFinanceiroChecklistResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPopFinanceiroChecklist extends ViewRecord
{
    protected static string $resource = PopFinanceiroChecklistResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
