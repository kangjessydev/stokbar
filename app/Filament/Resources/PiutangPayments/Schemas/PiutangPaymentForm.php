<?php

namespace App\Filament\Resources\PiutangPayments\Schemas;

use Filament\Schemas\Schema;
use Filament\Support\RawJs;

class PiutangPaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Select::make('piutang_id')
                    ->relationship('piutang', 'id', fn ($query) => $query->join('invoices', 'piutangs.invoice_id', '=', 'invoices.id')->select('piutangs.*', 'invoices.no_invoice'))
                    ->getOptionLabelFromRecordUsing(fn ($record) => "Invoice: {$record->no_invoice} (Sisa: Rp " . number_format($record->amount - $record->paid_amount, 0, ',', '.') . ")")
                    ->required()
                    ->searchable()
                    ->preload(),
                \Filament\Forms\Components\DatePicker::make('payment_date')
                    ->label('Tanggal Pembayaran')
                    ->default(now())
                    ->required(),
                \Filament\Forms\Components\Select::make('payment_method')
                    ->label('Metode')
                    ->options([
                        'cash' => 'Tunai (Cash)',
                        'transfer' => 'Transfer Bank',
                    ])
                    ->default('cash')
                    ->required(),
                \Filament\Forms\Components\TextInput::make('amount_paid')
                    ->label('Nominal Tunai/Transfer (Rp)')
                    ->prefix('Rp')
                    ->default(0)
                    ->required()
                    ->mask(\Filament\Support\RawJs::make(<<<'JS'
                        $input ? (function() {
                            let clean = $input.replace(/\D/g, '');
                            let len = clean.length;
                            if (len <= 3) return '9'.repeat(len);
                            let remainder = len % 3;
                            let parts = [];
                            if (remainder > 0) parts.push('9'.repeat(remainder));
                            let groups = Math.floor(len / 3);
                            for (let i = 0; i < groups; i++) parts.push('999');
                            return parts.join('.');
                        })() : ''
                    JS
                    ))
                    ->stripCharacters('.'),
                \Filament\Forms\Components\TextInput::make('reference_no')
                    ->label('No. Referensi / Catatan'),
                    
                \Filament\Forms\Components\Repeater::make('barangSisas')
                    ->relationship('barangSisas')
                    ->label('Retur Barang Sisa (Potong Piutang)')
                    ->schema([
                        \Filament\Forms\Components\Select::make('barang_id')
                            ->relationship('barang', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $barang = \App\Models\Barang::find($state);
                                $price = $barang ? $barang->price : 0;
                                $set('harga_jual', $price);
                                $qty = (int) ($get('qty_retur') ?? 0);
                                $set('subtotal_kredit', $price * $qty);
                            }),
                        \Filament\Forms\Components\TextInput::make('qty_retur')
                            ->label('Jumlah Retur (Bal/Dus)')
                            ->numeric()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $price = (int) str_replace('.', '', $get('harga_jual') ?? '0');
                                $set('subtotal_kredit', $price * (int) $state);
                            }),
                        \Filament\Forms\Components\TextInput::make('harga_jual')
                            ->label('Harga Jual Asli (Rp)')
                            ->prefix('Rp')
                            ->mask(\Filament\Support\RawJs::make(<<<'JS'
                        $input ? (function() {
                            let clean = $input.replace(/\D/g, '');
                            let len = clean.length;
                            if (len <= 3) return '9'.repeat(len);
                            let remainder = len % 3;
                            let parts = [];
                            if (remainder > 0) parts.push('9'.repeat(remainder));
                            let groups = Math.floor(len / 3);
                            for (let i = 0; i < groups; i++) parts.push('999');
                            return parts.join('.');
                        })() : ''
                    JS
                    ))
                            ->stripCharacters('.')
                            ->readOnly(),
                        \Filament\Forms\Components\TextInput::make('subtotal_kredit')
                            ->label('Potongan Retur (Rp)')
                            ->prefix('Rp')
                            ->mask(\Filament\Support\RawJs::make(<<<'JS'
                        $input ? (function() {
                            let clean = $input.replace(/\D/g, '');
                            let len = clean.length;
                            if (len <= 3) return '9'.repeat(len);
                            let remainder = len % 3;
                            let parts = [];
                            if (remainder > 0) parts.push('9'.repeat(remainder));
                            let groups = Math.floor(len / 3);
                            for (let i = 0; i < groups; i++) parts.push('999');
                            return parts.join('.');
                        })() : ''
                    JS
                    ))
                            ->stripCharacters('.')
                            ->readOnly(),
                    ])
                    ->columns(4)
                    ->defaultItems(0)
            ]);
    }
}
