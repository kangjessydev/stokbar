<?php

namespace App\Filament\Resources\Invoices\Schemas;

use App\Models\Barang;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class InvoiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('no_invoice')
                    ->label('Nomor Invoice')
                    ->default(fn () => 'INV-' . date('YmdHis'))
                    ->required()
                    ->readOnly()
                    ->unique(ignoreRecord: true),
                Select::make('customer_id')
                    ->label('Customer / Toko')
                    ->relationship('customer', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('sales_car_id')
                    ->label('Mobil Sales / Canvas')
                    ->relationship('salesCar', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                DatePicker::make('tanggal')
                    ->label('Tanggal Penjualan')
                    ->default(now())
                    ->required(),
                Select::make('payment_status')
                    ->label('Metode Pembayaran')
                    ->options([
                        'lunas' => 'Lunas Tunai',
                        'kredit' => 'Kredit (Tempo / Piutang)',
                    ])
                    ->default('lunas')
                    ->required()
                    ->live(),

                Repeater::make('invoiceItems')
                    ->relationship('invoiceItems')
                    ->label('Daftar Produk yang Terjual')
                    ->schema([
                        Select::make('barang_id')
                            ->label('Barang Jadi')
                            ->relationship('barang', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $barang = Barang::find($state);
                                $price = $barang ? $barang->price : 0;
                                $set('price', $price);
                                $qty = $get('qty') ?? 0;
                                $set('subtotal', $price * $qty);
                            }),
                        TextInput::make('qty')
                            ->label('Jumlah (Pcs)')
                            ->numeric()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $price = $get('price') ?? 0;
                                $set('subtotal', $price * $state);
                            }),
                        TextInput::make('price')
                            ->label('Harga (Rp)')
                            ->numeric()
                            ->prefix('Rp')
                            ->readOnly(),
                        TextInput::make('subtotal')
                            ->label('Subtotal (Rp)')
                            ->numeric()
                            ->prefix('Rp')
                            ->readOnly(),
                    ])
                    ->columns(4)
                    ->defaultItems(1)
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (callable $set, callable $get) {
                        $items = $get('invoiceItems') ?? [];
                        $total = 0;
                        foreach ($items as $item) {
                            $total += ($item['subtotal'] ?? 0);
                        }
                        $set('total_price', $total);
                    }),

                TextInput::make('total_price')
                    ->label('Total Transaksi (Rp)')
                    ->numeric()
                    ->prefix('Rp')
                    ->readOnly()
                    ->default(0.00),
            ]);
    }
}
