<?php

namespace App\Filament\Resources\Tarefas;

use App\Filament\Resources\Tarefas\Pages\CreateTarefa;
use App\Filament\Resources\Tarefas\Pages\EditTarefa;
use App\Filament\Resources\Tarefas\Pages\ListTarefas;
use App\Filament\Resources\Tarefas\Pages\ViewTarefa;
use App\Filament\Resources\Tarefas\Schemas\TarefaForm;
use App\Filament\Resources\Tarefas\Schemas\TarefaInfolist;
use App\Filament\Resources\Tarefas\Tables\TarefasTable;
use App\Models\Tarefa;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TarefaResource extends Resource
{
    protected static ?string $model = Tarefa::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQueueList;

    protected static ?string $modelLabel = 'tarefa';

    protected static ?string $pluralModelLabel = 'tarefas';

    protected static ?string $navigationLabel = 'Minhas demandas';

    protected static string|\UnitEnum|null $navigationGroup = 'Operação';

    protected static ?int $navigationSort = 15;

    public static function form(Schema $schema): Schema
    {
        return TarefaForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TarefaInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TarefasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTarefas::route('/'),
            'create' => CreateTarefa::route('/create'),
            'view' => ViewTarefa::route('/{record}'),
            'edit' => EditTarefa::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
