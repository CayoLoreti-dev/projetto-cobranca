<?php

namespace App\Filament\Resources\NotasFiscais\Tables;

use App\Models\NotaFiscal;
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

class NotasFiscaisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('emitida_em', 'desc')
            ->columns([
                TextColumn::make('cobranca.cliente.nome')
                    ->label('Cliente')
                    ->searchable(),
                TextColumn::make('cobranca.codigo')
                    ->label('Cobrança')
                    ->searchable(),
                TextColumn::make('boleto.linha_digitavel')
                    ->label('Boleto')
                    ->searchable()
                    ->limit(24)
                    ->placeholder('-'),
                TextColumn::make('numero')
                    ->label('Número')
                    ->searchable()
                    ->placeholder('-'),
                TextColumn::make('serie')
                    ->label('Série')
                    ->searchable()
                    ->placeholder('-'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->searchable(),
                TextColumn::make('valor')
                    ->label('Valor')
                    ->money('BRL')
                    ->sortable(),
                TextColumn::make('emitida_em')
                    ->label('Emitida em')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('-'),
                TextColumn::make('competencia')
                    ->label('Competência')
                    ->date()
                    ->sortable()
                    ->placeholder('-'),
                IconColumn::make('pdf_path')
                    ->label('PDF')
                    ->boolean()
                    ->getStateUsing(fn (NotaFiscal $record): bool => filled($record->pdf_path))
                    ->trueIcon(Heroicon::PaperClip)
                    ->falseIcon(Heroicon::XMark)
                    ->trueColor('success')
                    ->falseColor('gray'),
                TextColumn::make('pdf_original_name')
                    ->label('Arquivo')
                    ->searchable()
                    ->limit(28)
                    ->placeholder('-'),
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
