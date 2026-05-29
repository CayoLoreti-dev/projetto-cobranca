<?php

namespace App\Filament\Resources\Tarefas\Schemas;

use App\Enums\TarefaStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class TarefaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('cliente_id'),
                TextInput::make('cobranca_id'),
                TextInput::make('assigned_to_id')
                    ->numeric(),
                TextInput::make('tipo')
                    ->required(),
                TextInput::make('titulo')
                    ->required(),
                Textarea::make('descricao')
                    ->columnSpanFull(),
                TextInput::make('prioridade')
                    ->required()
                    ->numeric()
                    ->default(2),
                Select::make('status')
                    ->options(TarefaStatus::class)
                    ->default('ABERTA')
                    ->required(),
                DateTimePicker::make('vence_em'),
                DateTimePicker::make('iniciada_em'),
                DateTimePicker::make('concluida_em'),
                TextInput::make('metadata'),
            ]);
    }
}
