<?php

namespace App\Filament\Resources\ResepBoms\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ResepBomsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('barang.name')
                    ->label('Barang Jadi')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('bahanBaku.name')
                    ->label('Bahan Baku Utama')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('qty_needed')
                    ->label('Jumlah Dibutuhkan')
                    ->numeric(decimalPlaces: 4)
                    ->sortable(),
                TextColumn::make('bahanBaku.unit')
                    ->label('Satuan')
                    ->badge()
                    ->color('gray'),
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
