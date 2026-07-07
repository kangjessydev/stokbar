<?php

namespace App\Filament\Resources\Hutangs\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class HutangForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('supplier_name')
                    ->label('Nama Supplier Bahan Baku')
                    ->required()
                    ->maxLength(255),
                TextInput::make('amount')
                    ->label('Total Hutang (Rp)')
                    ->numeric()
                    ->prefix('Rp')
                    ->required()
                    ->disabled(fn ($record) => $record && $record->status === 'lunas'),
                TextInput::make('paid_amount')
                    ->label('Nominal Terbayar (Rp)')
                    ->numeric()
                    ->prefix('Rp')
                    ->readOnly()
                    ->default(0.00),
                Select::make('status')
                    ->label('Status Pelunasan')
                    ->options([
                        'belum_lunas' => 'Belum Lunas',
                        'lunas' => 'Lunas',
                    ])
                    ->disabled()
                    ->required(),
                DatePicker::make('tanggal')
                    ->label('Tanggal Pembelian Bahan')
                    ->default(now())
                    ->required()
                    ->disabled(fn ($record) => $record && $record->status === 'lunas'),
                DatePicker::make('due_date')
                    ->label('Tanggal Jatuh Tempo')
                    ->required(),
                
                Repeater::make('hutangPayments')
                    ->relationship('hutangPayments')
                    ->label('Catatan Transaksi Pembayaran Hutang')
                    ->schema([
                        TextInput::make('amount_paid')
                            ->label('Nominal Bayar (Rp)')
                            ->numeric()
                            ->prefix('Rp')
                            ->required(),
                        DatePicker::make('payment_date')
                            ->label('Tanggal Bayar')
                            ->default(now())
                            ->required(),
                        Select::make('payment_method')
                            ->label('Metode Bayar')
                            ->options([
                                'cash' => 'Tunai / Cash',
                                'transfer' => 'Transfer Bank',
                            ])
                            ->default('cash')
                            ->required(),
                        TextInput::make('reference_no')
                            ->label('Nomor Slip / Bukti')
                            ->maxLength(255),
                    ])
                    ->columns(4)
                    ->defaultItems(0),
            ]);
    }
}
