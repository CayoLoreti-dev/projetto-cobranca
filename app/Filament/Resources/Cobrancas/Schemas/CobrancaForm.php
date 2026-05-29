<?php

namespace App\Filament\Resources\Cobrancas\Schemas;

use App\Enums\CobrancaStatus;
use App\Enums\CobrancaTipo;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class CobrancaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('cliente_id')
                    ->label('Cliente')
                    ->relationship('cliente', 'nome')
                    ->searchable()
                    ->required(),
                TextInput::make('codigo')
                    ->label('Código')
                    ->required(),
                TextInput::make('categoria')
                    ->label('Categoria'),
                Select::make('tipo')
                    ->label('Tipo')
                    ->options(CobrancaTipo::class)
                    ->required(),
                TextInput::make('valor_total')
                    ->label('Valor total')
                    ->required()
                    ->numeric(),
                TextInput::make('moeda')
                    ->label('Moeda')
                    ->required()
                    ->default('BRL'),
                Select::make('status')
                    ->label('Status')
                    ->options(CobrancaStatus::class)
                    ->default('EMITIDA')
                    ->required(),
                DatePicker::make('data_emissao')
                    ->label('Data de emissão')
                    ->required(),
                DatePicker::make('data_vencimento_principal')
                    ->label('Vencimento principal')
                    ->required(),
                Select::make('responsavel_id')
                    ->label('Responsável')
                    ->relationship('responsavel', 'name')
                    ->searchable()
                    ->placeholder('Sem responsável'),
                TextInput::make('prioridade')
                    ->label('Prioridade')
                    ->required()
                    ->numeric()
                    ->default(2),
                Textarea::make('proxima_acao')
                    ->label('Próxima ação')
                    ->columnSpanFull(),
                DatePicker::make('data_proxima_acao')
                    ->label('Próxima ação em'),
                Textarea::make('observacoes')
                    ->label('Observações')
                    ->columnSpanFull(),
                TextInput::make('metadata')
                    ->label('Metadados'),
                DateTimePicker::make('archived_at')
                    ->label('Arquivado em'),
            ]);
    }
}
