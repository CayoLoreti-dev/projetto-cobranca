<?php

namespace App\Filament\Resources\Tarefas\Pages;

use App\Filament\Resources\Tarefas\TarefaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTarefas extends ListRecords
{
    protected static string $resource = TarefaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
