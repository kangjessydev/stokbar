<?php

namespace App\Filament\Resources\CanvasReturs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CanvasRetursTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('salesCar.name')
                    ->label('Mobil Sales')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('barang.name')
                    ->label('Barang Jadi')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('qty_returned')
                    ->label('Jumlah Retur')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('tanggal')
                    ->label('Tanggal Retur')
                    ->date()
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
