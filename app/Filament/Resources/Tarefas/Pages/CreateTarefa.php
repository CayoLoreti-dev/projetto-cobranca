<?php

namespace App\Filament\Resources\Tarefas\Pages;

use App\Filament\Resources\Tarefas\TarefaResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTarefa extends CreateRecord
{
    protected static string $resource = TarefaResource::class;
}
