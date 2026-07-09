<?php

namespace App\Filament\Resources\PiutangPayments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;

class PiutangPaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('payment_date')
                    ->date()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('piutang.invoice.no_invoice')
                    ->label('Piutang Invoice')
                    ->searchable()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('payment_method')
                    ->badge(),
                \Filament\Tables\Columns\TextColumn::make('amount_paid')
                    ->label('Nominal Tunai')
                    ->money('IDR')
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('barangSisas_count')
                    ->counts('barangSisas')
                    ->label('Item Retur'),
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
