<?php

namespace App\Filament\Widgets;

use App\Models\Piutang;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Carbon;

class PiutangJatuhTempo extends BaseWidget
{
    protected static ?int $sort = 4;
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Piutang::query()
                    ->where('status', 'belum_lunas')
                    ->where('due_date', '<=', Carbon::now()->addDays(7)->toDateString())
                    ->with(['invoice.customer', 'invoice.salesCar'])
                    ->orderBy('due_date', 'asc')
            )
            ->heading('⚠️ Piutang Jatuh Tempo (7 Hari ke Depan)')
            ->description('Daftar customer yang perlu segera ditagih')
            ->columns([
                Tables\Columns\TextColumn::make('invoice.customer.name')
                    ->label('Customer / Toko')
                    ->weight('bold')
                    ->searchable(),

                Tables\Columns\TextColumn::make('invoice.no_invoice')
                    ->label('No. Invoice')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('invoice.salesCar.driver_name')
                    ->label('Sales')
                    ->icon('heroicon-m-user'),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Total Piutang')
                    ->money('IDR', locale: 'id')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('paid_amount')
                    ->label('Terbayar')
                    ->money('IDR', locale: 'id')
                    ->color('success'),

                Tables\Columns\TextColumn::make('sisa')
                    ->label('Sisa Tagihan')
                    ->state(fn ($record) => $record->amount - $record->paid_amount)
                    ->money('IDR', locale: 'id')
                    ->color('danger')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('due_date')
                    ->label('Jatuh Tempo')
                    ->date('d M Y')
                    ->badge()
                    ->color(fn ($record) => Carbon::parse($record->due_date)->lt(now()) ? 'danger' : 'warning')
                    ->description(function ($record) {
                        $diff = Carbon::now()->startOfDay()->diffInDays(Carbon::parse($record->due_date)->startOfDay(), false);
                        if ($diff < 0) {
                            return '🔴 Telat ' . abs($diff) . ' hari!';
                        }
                        if ($diff === 0) {
                            return '⚠️ Jatuh tempo HARI INI';
                        }
                        return '🟡 ' . $diff . ' hari lagi';
                    })
                    ->sortable(),
            ])
            ->paginated(false)
            ->striped();
    }
}
