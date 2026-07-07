<?php

namespace App\Filament\Resources\Barangs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BarangsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('gudang.name')
                    ->label('Gudang')
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Nama Barang Jadi')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('stock')
                    ->label('Stok')
                    ->numeric()
                    ->sortable()
                    ->color(fn ($record) => $record->stock <= 10 ? 'danger' : 'success')
                    ->description(fn ($record) => $record->stock <= 10 ? '⚠️ Stok Menipis!' : null),
                TextColumn::make('unit')
                    ->label('Satuan')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('price')
                    ->label('Harga Jual')
                    ->money('idr', locale: 'id_ID')
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
