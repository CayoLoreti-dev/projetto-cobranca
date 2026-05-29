<?php

namespace App\Filament\Resources\NotasFiscais;

use App\Filament\Resources\NotasFiscais\Pages\CreateNotaFiscal;
use App\Filament\Resources\NotasFiscais\Pages\EditNotaFiscal;
use App\Filament\Resources\NotasFiscais\Pages\ListNotasFiscais;
use App\Filament\Resources\NotasFiscais\Pages\ViewNotaFiscal;
use App\Filament\Resources\NotasFiscais\Schemas\NotaFiscalForm;
use App\Filament\Resources\NotasFiscais\Schemas\NotaFiscalInfolist;
use App\Filament\Resources\NotasFiscais\Tables\NotasFiscaisTable;
use App\Models\NotaFiscal;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class NotaFiscalResource extends Resource
{
    protected static ?string $model = NotaFiscal::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $modelLabel = 'nota fiscal';

    protected static ?string $pluralModelLabel = 'notas fiscais';

    protected static ?string $navigationLabel = 'Notas fiscais';

    protected static string|\UnitEnum|null $navigationGroup = 'Financeiro';

    protected static ?int $navigationSort = 41;

    public static function form(Schema $schema): Schema
    {
        return NotaFiscalForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return NotaFiscalInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return NotasFiscaisTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with([
                'cobranca.cliente',
                'boleto',
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListNotasFiscais::route('/'),
            'create' => CreateNotaFiscal::route('/create'),
            'view' => ViewNotaFiscal::route('/{record}'),
            'edit' => EditNotaFiscal::route('/{record}/edit'),
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
