<?php

namespace App\Filament\Resources\NotasFiscais\Schemas;

use App\Enums\NotaFiscalStatus;
use App\Filament\Support\BillingSelectOptions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class NotaFiscalForm
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
                Select::make('boleto_id')
                    ->label('Boleto')
                    ->searchable()
                    ->options(fn (): array => BillingSelectOptions::boletos())
                    ->getSearchResultsUsing(fn (?string $search): array => BillingSelectOptions::boletos($search))
                    ->getOptionLabelUsing(fn (mixed $value): ?string => BillingSelectOptions::boletoLabelForId($value))
                    ->placeholder('Opcional')
                    ->rules(['nullable', 'exists:boletos,id']),
                TextInput::make('numero')
                    ->label('Número')
                    ->maxLength(255),
                TextInput::make('serie')
                    ->label('Série')
                    ->maxLength(255),
                Select::make('status')
                    ->label('Status')
                    ->options(NotaFiscalStatus::class)
                    ->default('PENDENTE_EMISSAO')
                    ->required(),
                TextInput::make('valor')
                    ->label('Valor')
                    ->required()
                    ->numeric(),
                DateTimePicker::make('emitida_em')
                    ->label('Emitida em'),
                DatePicker::make('competencia')
                    ->label('Competência'),
                FileUpload::make('pdf_path')
                    ->label('PDF da nota fiscal')
                    ->disk('local')
                    ->directory('notas-fiscais')
                    ->acceptedFileTypes(['application/pdf'])
                    ->maxSize(10240)
                    ->downloadable()
                    ->openable()
                    ->previewable(false)
                    ->storeFileNamesIn('pdf_original_name')
                    ->preventFilePathTampering()
                    ->helperText('Envie apenas PDF. O arquivo fica no armazenamento privado do Laravel.')
                    ->columnSpanFull(),
                Textarea::make('observacoes')
                    ->label('Observações')
                    ->columnSpanFull()
                    ->maxLength(2000),
                TextInput::make('metadata')
                    ->label('Metadados')
                    ->rules(['nullable', 'json']),
            ]);
    }
}
