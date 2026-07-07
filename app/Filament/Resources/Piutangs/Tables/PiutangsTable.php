<?php

namespace App\Filament\Resources\Piutangs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PiutangsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice.no_invoice')
                    ->label('No. Invoice')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('invoice.customer.name')
                    ->label('Customer / Toko')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('amount')
                    ->label('Total Piutang')
                    ->money('idr', locale: 'id_ID')
                    ->sortable(),
                TextColumn::make('paid_amount')
                    ->label('Terbayar')
                    ->money('idr', locale: 'id_ID')
                    ->sortable(),
                TextColumn::make('remaining')
                    ->label('Sisa Piutang')
                    ->money('idr', locale: 'id_ID')
                    ->state(fn ($record) => $record->amount - $record->paid_amount)
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'lunas' => 'success',
                        'belum_lunas' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'lunas' => 'Lunas',
                        'belum_lunas' => 'Belum Lunas',
                        default => $state,
                    })
                    ->sortable(),
                TextColumn::make('due_date')
                    ->label('Jatuh Tempo')
                    ->date()
                    ->color(fn ($record) => $record->status === 'belum_lunas' && now()->gt($record->due_date) ? 'danger' : 'gray')
                    ->description(fn ($record) => $record->status === 'belum_lunas' && now()->gt($record->due_date) ? '⚠️ TERLEWAT TEMPO!' : null)
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
