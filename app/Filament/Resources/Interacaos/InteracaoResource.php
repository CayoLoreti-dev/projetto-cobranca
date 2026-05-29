<?php

namespace App\Filament\Resources\Interacaos;

use App\Filament\Resources\Interacaos\Pages\CreateInteracao;
use App\Filament\Resources\Interacaos\Pages\EditInteracao;
use App\Filament\Resources\Interacaos\Pages\ListInteracaos;
use App\Filament\Resources\Interacaos\Pages\ViewInteracao;
use App\Filament\Resources\Interacaos\Schemas\InteracaoForm;
use App\Filament\Resources\Interacaos\Schemas\InteracaoInfolist;
use App\Filament\Resources\Interacaos\Tables\InteracaosTable;
use App\Models\Interacao;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class InteracaoResource extends Resource
{
    protected static ?string $model = Interacao::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static ?string $modelLabel = 'interação';

    protected static ?string $pluralModelLabel = 'interações';

    protected static ?string $navigationLabel = 'Histórico';

    protected static string|\UnitEnum|null $navigationGroup = 'Operação';

    protected static ?int $navigationSort = 18;

    public static function form(Schema $schema): Schema
    {
        return InteracaoForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return InteracaoInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InteracaosTable::configure($table);
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
            'index' => ListInteracaos::route('/'),
            'create' => CreateInteracao::route('/create'),
            'view' => ViewInteracao::route('/{record}'),
            'edit' => EditInteracao::route('/{record}/edit'),
        ];
    }
}
