<?php

namespace App\Filament\Resources\SerasaOcorrencias\Schemas;

use App\Enums\SerasaEtapa;
use App\Enums\SerasaStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class SerasaOcorrenciaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('cobranca_id')
                    ->label('Cobrança')
                    ->relationship('cobranca', 'codigo')
                    ->searchable()
                    ->required(),
                Select::make('responsavel_id')
                    ->label('Responsável')
                    ->relationship('responsavel', 'name')
                    ->searchable()
                    ->placeholder('Opcional'),
                Select::make('etapa')
                    ->label('Etapa')
                    ->options(SerasaEtapa::class)
                    ->required(),
                Select::make('status')
                    ->label('Status')
                    ->options(SerasaStatus::class)
                    ->default('PENDENTE')
                    ->required(),
                DateTimePicker::make('agendado_para')
                    ->label('Agendado para'),
                DateTimePicker::make('executado_em')
                    ->label('Executado em'),
                Textarea::make('observacoes')
                    ->label('Observações')
                    ->columnSpanFull(),
                Textarea::make('metadata')
                    ->label('Metadados')
                    ->columnSpanFull(),
            ]);
    }
}