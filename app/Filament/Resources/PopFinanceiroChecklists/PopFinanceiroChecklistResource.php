<?php

namespace App\Filament\Resources\PopFinanceiroChecklists;

use App\Filament\Resources\PopFinanceiroChecklists\Pages\CreatePopFinanceiroChecklist;
use App\Filament\Resources\PopFinanceiroChecklists\Pages\EditPopFinanceiroChecklist;
use App\Filament\Resources\PopFinanceiroChecklists\Pages\ListPopFinanceiroChecklists;
use App\Filament\Resources\PopFinanceiroChecklists\Pages\ViewPopFinanceiroChecklist;
use App\Filament\Resources\PopFinanceiroChecklists\Schemas\PopFinanceiroChecklistForm;
use App\Filament\Resources\PopFinanceiroChecklists\Schemas\PopFinanceiroChecklistInfolist;
use App\Filament\Resources\PopFinanceiroChecklists\Tables\PopFinanceiroChecklistsTable;
use App\Models\PopFinanceiroChecklist;
use BackedEnum;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PopFinanceiroChecklistResource extends Resource
{
    protected static ?string $model = PopFinanceiroChecklist::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static ?string $modelLabel = 'checklist POP';

    protected static ?string $pluralModelLabel = 'checklists POP';

    protected static ?string $navigationLabel = 'POP Financeiro';

    protected static string|\UnitEnum|null $navigationGroup = 'Financeiro';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return PopFinanceiroChecklistForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PopFinanceiroChecklistInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PopFinanceiroChecklistsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with([
                'cliente',
                'cobranca',
                'parcela',
                'responsavel',
            ]);
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
            'index' => ListPopFinanceiroChecklists::route('/'),
            'create' => CreatePopFinanceiroChecklist::route('/create'),
            'view' => ViewPopFinanceiroChecklist::route('/{record}'),
            'edit' => EditPopFinanceiroChecklist::route('/{record}/edit'),
        ];
    }
}
