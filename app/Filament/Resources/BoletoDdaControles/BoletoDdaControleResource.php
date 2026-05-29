<?php

namespace App\Filament\Resources\BoletoDdaControles;

use App\Filament\Resources\BoletoDdaControles\Pages\CreateBoletoDdaControle;
use App\Filament\Resources\BoletoDdaControles\Pages\EditBoletoDdaControle;
use App\Filament\Resources\BoletoDdaControles\Pages\ListBoletoDdaControles;
use App\Filament\Resources\BoletoDdaControles\Pages\ViewBoletoDdaControle;
use App\Filament\Resources\BoletoDdaControles\Schemas\BoletoDdaControleForm;
use App\Filament\Resources\BoletoDdaControles\Schemas\BoletoDdaControleInfolist;
use App\Filament\Resources\BoletoDdaControles\Tables\BoletoDdaControlesTable;
use App\Models\BoletoDdaControle;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BoletoDdaControleResource extends Resource
{
    protected static ?string $model = BoletoDdaControle::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?string $modelLabel = 'controle DDA';

    protected static ?string $pluralModelLabel = 'controles DDA';

    protected static ?string $navigationLabel = 'Controle DDA';

    protected static string|\UnitEnum|null $navigationGroup = 'Financeiro';

    protected static ?int $navigationSort = 42;

    public static function form(Schema $schema): Schema
    {
        return BoletoDdaControleForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return BoletoDdaControleInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BoletoDdaControlesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBoletoDdaControles::route('/'),
            'create' => CreateBoletoDdaControle::route('/create'),
            'view' => ViewBoletoDdaControle::route('/{record}'),
            'edit' => EditBoletoDdaControle::route('/{record}/edit'),
        ];
    }
}