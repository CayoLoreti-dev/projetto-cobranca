<?php

namespace App\Filament\Resources\Boletos;

use App\Filament\Resources\Boletos\Pages\CreateBoleto;
use App\Filament\Resources\Boletos\Pages\EditBoleto;
use App\Filament\Resources\Boletos\Pages\ListBoletos;
use App\Filament\Resources\Boletos\Pages\ViewBoleto;
use App\Filament\Resources\Boletos\Schemas\BoletoForm;
use App\Filament\Resources\Boletos\Schemas\BoletoInfolist;
use App\Filament\Resources\Boletos\Tables\BoletosTable;
use App\Models\Boleto;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BoletoResource extends Resource
{
    protected static ?string $model = Boleto::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedReceiptPercent;

    protected static ?string $modelLabel = 'boleto';

    protected static ?string $pluralModelLabel = 'boletos';

    protected static ?string $navigationLabel = 'Boletos';

    protected static string|\UnitEnum|null $navigationGroup = 'Financeiro';

    protected static ?int $navigationSort = 40;

    public static function form(Schema $schema): Schema
    {
        return BoletoForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return BoletoInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BoletosTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with([
                'cobranca.cliente',
                'parcela',
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
            'index' => ListBoletos::route('/'),
            'create' => CreateBoleto::route('/create'),
            'view' => ViewBoleto::route('/{record}'),
            'edit' => EditBoleto::route('/{record}/edit'),
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
