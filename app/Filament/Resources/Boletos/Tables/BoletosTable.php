<?php

namespace App\Filament\Resources\Boletos\Tables;

use App\Models\Boleto;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class BoletosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('vencimento')
            ->columns([
                TextColumn::make('cobranca.cliente.nome')
                    ->label('Cliente')
                    ->searchable(),
                TextColumn::make('cobranca.codigo')
                    ->label('Cobrança')
                    ->searchable(),
                TextColumn::make('parcela.numero')
                    ->label('Parcela')
                    ->numeric()
                    ->placeholder('-'),
                TextColumn::make('linha_digitavel')
                    ->label('Linha digitável')
                    ->searchable()
                    ->limit(28)
                    ->placeholder('-'),
                TextColumn::make('valor')
                    ->label('Valor')
                    ->money('BRL')
                    ->sortable(),
                TextColumn::make('vencimento')
                    ->label('Vencimento')
                    ->date()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->searchable(),
                IconColumn::make('pdf_path')
                    ->label('PDF')
                    ->boolean()
                    ->getStateUsing(fn (Boleto $record): bool => filled($record->pdf_path))
                    ->trueIcon(Heroicon::PaperClip)
                    ->falseIcon(Heroicon::XMark)
                    ->trueColor('success')
                    ->falseColor('gray'),
                TextColumn::make('pdf_original_name')
                    ->label('Arquivo')
                    ->searchable()
                    ->limit(28)
                    ->placeholder('-'),
                TextColumn::make('gerado_em')
                    ->label('Gerado em')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('enviado_em')
                    ->label('Enviado em')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('lido_em')
                    ->label('Lido em')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('recebido_em')
                    ->label('Recebido em')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('pago_em')
                    ->label('Pago em')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('pdf_url')
                    ->label('URL externa')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('id')
                    ->label('ID')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Atualizado em')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->label('Excluído em')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
