<?php

namespace App\Filament\Resources\Cobrancas;

use App\Filament\Resources\Cobrancas\Pages\CreateCobranca;
use App\Filament\Resources\Cobrancas\Pages\EditCobranca;
use App\Filament\Resources\Cobrancas\Pages\ListCobrancas;
use App\Filament\Resources\Cobrancas\Pages\ViewCobranca;
use App\Filament\Resources\Cobrancas\Schemas\CobrancaForm;
use App\Filament\Resources\Cobrancas\Schemas\CobrancaInfolist;
use App\Filament\Resources\Cobrancas\Tables\CobrancasTable;
use App\Models\Cobranca;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CobrancaResource extends Resource
{
    protected static ?string $model = Cobranca::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWallet;

    protected static ?string $modelLabel = 'cobrança';

    protected static ?string $pluralModelLabel = 'cobranças';

    protected static ?string $navigationLabel = 'Cobranças';

    protected static string|\UnitEnum|null $navigationGroup = 'Financeiro';

    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return CobrancaForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CobrancaInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CobrancasTable::configure($table);
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
            'index' => ListCobrancas::route('/'),
            'create' => CreateCobranca::route('/create'),
            'view' => ViewCobranca::route('/{record}'),
            'edit' => EditCobranca::route('/{record}/edit'),
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
