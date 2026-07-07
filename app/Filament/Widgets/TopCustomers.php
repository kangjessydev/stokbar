<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class TopCustomers extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Customer::withSum('invoices', 'total_price')
                    ->orderByDesc('invoices_sum_total_price')
                    ->limit(5)
            )
            ->heading('Top 5 Customer (Berdasarkan Total Pembelian)')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Customer')
                    ->weight('bold')
                    ->searchable(),
                Tables\Columns\TextColumn::make('market')
                    ->label('Pasar / Area')
                    ->badge(),
                Tables\Columns\TextColumn::make('invoices_sum_total_price')
                    ->label('Total Transaksi Historis')
                    ->money('IDR', locale: 'id')
                    ->color('success')
                    ->weight('bold')
                    ->sortable(),
            ])
            ->paginated(false);
    }
}
