<?php

namespace App\Filament\Resources\PopFinanceiroChecklists\Schemas;

use App\Enums\PopChecklistStatus;
use App\Filament\Support\BillingSelectOptions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PopFinanceiroChecklistForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('reference_date')
                    ->label('Data de referência')
                    ->required(),
                Select::make('cliente_id')
                    ->label('Cliente')
                    ->searchable()
                    ->options(fn (): array => BillingSelectOptions::clientes())
                    ->getSearchResultsUsing(fn (?string $search): array => BillingSelectOptions::clientes($search))
                    ->getOptionLabelUsing(fn (mixed $value): ?string => BillingSelectOptions::clienteLabelForId($value))
                    ->placeholder('Opcional')
                    ->rules(['nullable', 'exists:clientes,id']),
                Select::make('cobranca_id')
                    ->label('Cliente / cobrança')
                    ->searchable()
                    ->options(fn (): array => BillingSelectOptions::cobrancas())
                    ->getSearchResultsUsing(fn (?string $search): array => BillingSelectOptions::cobrancas($search))
                    ->getOptionLabelUsing(fn (mixed $value): ?string => BillingSelectOptions::cobrancaLabelForId($value))
                    ->placeholder('Opcional')
                    ->rules(['nullable', 'exists:cobrancas,id']),
                Select::make('parcela_id')
                    ->label('Parcela')
                    ->searchable()
                    ->options(fn (): array => BillingSelectOptions::parcelas())
                    ->getSearchResultsUsing(fn (?string $search): array => BillingSelectOptions::parcelas($search))
                    ->getOptionLabelUsing(fn (mixed $value): ?string => BillingSelectOptions::parcelaLabelForId($value))
                    ->placeholder('Opcional')
                    ->rules(['nullable', 'exists:parcelas,id']),
                Select::make('boleto_id')
                    ->label('Boleto')
                    ->searchable()
                    ->options(fn (): array => BillingSelectOptions::boletos())
                    ->getSearchResultsUsing(fn (?string $search): array => BillingSelectOptions::boletos($search))
                    ->getOptionLabelUsing(fn (mixed $value): ?string => BillingSelectOptions::boletoLabelForId($value))
                    ->placeholder('Opcional')
                    ->rules(['nullable', 'exists:boletos,id']),
                Select::make('assigned_to_id')
                    ->label('Responsável')
                    ->searchable()
                    ->options(fn (): array => BillingSelectOptions::usuarios())
                    ->getSearchResultsUsing(fn (?string $search): array => BillingSelectOptions::usuarios($search))
                    ->getOptionLabelUsing(fn (mixed $value): ?string => BillingSelectOptions::usuarioLabelForId($value))
                    ->placeholder('Sem responsável')
                    ->rules(['nullable', 'exists:users,id']),
                TextInput::make('etapa')
                    ->label('Etapa POP')
                    ->required(),
                Select::make('status')
                    ->label('Status')
                    ->options(PopChecklistStatus::class)
                    ->default('PENDENTE')
                    ->required(),
                TextInput::make('acao_canal')
                    ->label('Canal da ação')
                    ->placeholder('SISTEMA / EMAIL / WHATSAPP'),
                TextInput::make('escalonamento_nivel')
                    ->label('Escalonamento')
                    ->placeholder('VENDEDOR / LARISSA / EDIVALDO / SERASA'),
                TextInput::make('titulo')
                    ->label('Título')
                    ->required()
                    ->maxLength(255),
                Textarea::make('descricao')
                    ->label('Descrição')
                    ->columnSpanFull()
                    ->maxLength(2000),
                DateTimePicker::make('sla_limite_em')
                    ->label('SLA limite'),
                DateTimePicker::make('concluido_em')
                    ->label('Concluído em'),
                Textarea::make('metadata')
                    ->label('Metadados')
                    ->columnSpanFull()
                    ->maxLength(2000)
                    ->helperText('JSON string allowed')
                    ->rules(['nullable', 'json']),
            ]);
    }
}
