<?php

namespace App\Filament\Resources\SystemSettings\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SystemSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('group')
                    ->required()
                    ->default('geral'),
                TextInput::make('value')
                    ->required(),
                TextInput::make('type')
                    ->required()
                    ->default('string'),
                Textarea::make('description')
                    ->columnSpanFull(),
                Toggle::make('is_encrypted')
                    ->required(),
                TextInput::make('updated_by_id')
                    ->numeric(),
            ]);
    }
}
