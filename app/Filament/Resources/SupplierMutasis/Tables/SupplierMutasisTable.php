<?php

namespace App\Filament\Resources\SupplierMutasis\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;

class SupplierMutasisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('tanggal')
                    ->date()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('supplier.name')
                    ->searchable()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('barang.name')
                    ->searchable()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('jenis_transaksi')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'barang_masuk' => 'success',
                        'barang_retur' => 'danger',
                        default => 'gray',
                    }),
                \Filament\Tables\Columns\TextColumn::make('qty_bal')
                    ->numeric()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('total_hutang')
                    ->money('IDR')
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
