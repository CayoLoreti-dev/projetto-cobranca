<?php

namespace App\Filament\Resources\SerasaOcorrencias;

use App\Filament\Resources\SerasaOcorrencias\Pages\CreateSerasaOcorrencia;
use App\Filament\Resources\SerasaOcorrencias\Pages\EditSerasaOcorrencia;
use App\Filament\Resources\SerasaOcorrencias\Pages\ListSerasaOcorrencias;
use App\Filament\Resources\SerasaOcorrencias\Pages\ViewSerasaOcorrencia;
use App\Filament\Resources\SerasaOcorrencias\Schemas\SerasaOcorrenciaForm;
use App\Filament\Resources\SerasaOcorrencias\Schemas\SerasaOcorrenciaInfolist;
use App\Filament\Resources\SerasaOcorrencias\Tables\SerasaOcorrenciasTable;
use App\Models\SerasaOcorrencia;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SerasaOcorrenciaResource extends Resource
{
    protected static ?string $model = SerasaOcorrencia::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldExclamation;

    protected static ?string $modelLabel = 'ocorrência Serasa';

    protected static ?string $pluralModelLabel = 'ocorrências Serasa';

    protected static ?string $navigationLabel = 'Ocorrências Serasa';

    protected static string|\UnitEnum|null $navigationGroup = 'Financeiro';

    protected static ?int $navigationSort = 43;

    public static function form(Schema $schema): Schema
    {
        return SerasaOcorrenciaForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SerasaOcorrenciaInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SerasaOcorrenciasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSerasaOcorrencias::route('/'),
            'create' => CreateSerasaOcorrencia::route('/create'),
            'view' => ViewSerasaOcorrencia::route('/{record}'),
            'edit' => EditSerasaOcorrencia::route('/{record}/edit'),
        ];
    }
}