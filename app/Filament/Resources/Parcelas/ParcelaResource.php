<?php

namespace App\Filament\Resources\Parcelas;

use App\Filament\Resources\Parcelas\Pages\CreateParcela;
use App\Filament\Resources\Parcelas\Pages\EditParcela;
use App\Filament\Resources\Parcelas\Pages\ListParcelas;
use App\Filament\Resources\Parcelas\Pages\ViewParcela;
use App\Filament\Resources\Parcelas\Schemas\ParcelaForm;
use App\Filament\Resources\Parcelas\Schemas\ParcelaInfolist;
use App\Filament\Resources\Parcelas\Tables\ParcelasTable;
use App\Models\Parcela;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ParcelaResource extends Resource
{
    protected static ?string $model = Parcela::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $modelLabel = 'parcela';

    protected static ?string $pluralModelLabel = 'parcelas';

    protected static ?string $navigationLabel = 'Parcelas';

    protected static string|\UnitEnum|null $navigationGroup = 'Financeiro';

    protected static ?int $navigationSort = 30;

    public static function form(Schema $schema): Schema
    {
        return ParcelaForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ParcelaInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ParcelasTable::configure($table);
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
            'index' => ListParcelas::route('/'),
            'create' => CreateParcela::route('/create'),
            'view' => ViewParcela::route('/{record}'),
            'edit' => EditParcela::route('/{record}/edit'),
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
