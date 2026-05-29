<?php

namespace App\Filament\Resources\Boletos\Schemas;

use App\Enums\BoletoStatus;
use App\Filament\Support\BillingSelectOptions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BoletoForm
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
                    ->rules(['nullable', 'exists:cobrancas,id']),
                Select::make('parcela_id')
                    ->label('Parcela')
                    ->searchable()
                    ->options(fn (): array => BillingSelectOptions::parcelas())
                    ->getSearchResultsUsing(fn (?string $search): array => BillingSelectOptions::parcelas($search))
                    ->getOptionLabelUsing(fn (mixed $value): ?string => BillingSelectOptions::parcelaLabelForId($value))
                    ->placeholder('Opcional')
                    ->rules(['nullable', 'exists:parcelas,id']),
                TextInput::make('linha_digitavel')
                    ->label('Linha digitável')
                    ->maxLength(255),
                TextInput::make('codigo_barras')
                    ->label('Código de barras')
                    ->maxLength(255),
                TextInput::make('valor')
                    ->label('Valor')
                    ->required()
                    ->numeric(),
                DatePicker::make('vencimento')
                    ->label('Vencimento')
                    ->required(),
                Select::make('status')
                    ->label('Status')
                    ->options(BoletoStatus::class)
                    ->default('EMITIDO')
                    ->required(),
                FileUpload::make('pdf_path')
                    ->label('PDF do boleto')
                    ->disk('local')
                    ->directory('boletos')
                    ->acceptedFileTypes(['application/pdf'])
                    ->maxSize(10240)
                    ->downloadable()
                    ->openable()
                    ->previewable(false)
                    ->storeFileNamesIn('pdf_original_name')
                    ->preventFilePathTampering()
                    ->helperText('Envie apenas PDF. O arquivo fica no armazenamento privado do Laravel.')
                    ->columnSpanFull(),
                DateTimePicker::make('gerado_em')
                    ->label('Gerado em'),
                DateTimePicker::make('enviado_em')
                    ->label('Enviado em'),
                DateTimePicker::make('lido_em')
                    ->label('Lido em'),
                DateTimePicker::make('recebido_em')
                    ->label('Recebido em'),
                DateTimePicker::make('pago_em')
                    ->label('Pago em'),
                TextInput::make('pdf_url')
                    ->label('URL externa do PDF')
                    ->url(),
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
