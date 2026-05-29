<?php

namespace App\Filament\Resources\PopFinanceiroChecklists\Pages;

use App\Filament\Resources\PopFinanceiroChecklists\PopFinanceiroChecklistResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPopFinanceiroChecklists extends ListRecords
{
    protected static string $resource = PopFinanceiroChecklistResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
