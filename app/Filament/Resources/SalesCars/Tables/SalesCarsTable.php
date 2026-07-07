<?php

namespace App\Filament\Resources\SalesCars\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SalesCarsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Identitas Mobil')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('driver_name')
                    ->label('Nama Driver')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Akun User Sales')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Belum Terhubung'),
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
