<?php

namespace App\Filament\Resources\SerasaOcorrencias\Schemas;

use App\Enums\SerasaEtapa;
use App\Enums\SerasaStatus;
use App\Filament\Support\BillingSelectOptions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SerasaOcorrenciaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('cobranca_id')
                    ->label('Cliente / cobrança')
                    ->searchable()
                    ->options(fn (): array => BillingSelectOptions::cobrancas())
                    ->getSearchResultsUsing(fn (?string $search): array => BillingSelectOptions::cobrancas($search))
                    ->getOptionLabelUsing(fn (mixed $value): ?string => BillingSelectOptions::cobrancaLabelForId($value))
                    ->placeholder('Pesquise por cliente, documento ou código')
                    ->required()
                    ->rules(['required', 'exists:cobrancas,id']),
                Select::make('responsavel_id')
                    ->label('Responsável')
                    ->searchable()
                    ->options(fn (): array => BillingSelectOptions::usuarios())
                    ->getSearchResultsUsing(fn (?string $search): array => BillingSelectOptions::usuarios($search))
                    ->getOptionLabelUsing(fn (mixed $value): ?string => BillingSelectOptions::usuarioLabelForId($value))
                    ->placeholder('Opcional')
                    ->rules(['nullable', 'exists:users,id']),
                Select::make('etapa')
                    ->label('Etapa SERASA')
                    ->options(SerasaEtapa::class)
                    ->required(),
                Select::make('status')
                    ->label('Status')
                    ->options(SerasaStatus::class)
                    ->default('PENDENTE')
                    ->required(),
                TextInput::make('protocolo')
                    ->label('Protocolo / remessa')
                    ->maxLength(255),
                TextInput::make('documento_devedor')
                    ->label('Documento do devedor')
                    ->maxLength(255),
                TextInput::make('valor_negativado')
                    ->label('Valor informado ao SERASA')
                    ->prefix('R$')
                    ->numeric(),
                DatePicker::make('data_limite_regularizacao')
                    ->label('Limite para regularização'),
                DateTimePicker::make('agendado_para')
                    ->label('Agendado para'),
                DateTimePicker::make('executado_em')
                    ->label('Executado em'),
                DateTimePicker::make('data_baixa')
                    ->label('Baixa em'),
                TextInput::make('motivo_baixa')
                    ->label('Motivo da baixa')
                    ->maxLength(255),
                Textarea::make('observacoes')
                    ->label('Observações')
                    ->columnSpanFull()
                    ->maxLength(2000),
                Textarea::make('metadata')
                    ->label('Metadados')
                    ->columnSpanFull()
                    ->maxLength(2000)
                    ->rules(['nullable', 'json']),
            ]);
    }
}
