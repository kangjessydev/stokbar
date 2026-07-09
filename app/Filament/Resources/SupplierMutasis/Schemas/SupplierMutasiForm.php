<?php

namespace App\Filament\Resources\SupplierMutasis\Schemas;

use Filament\Schemas\Schema;
use Filament\Support\RawJs;

class SupplierMutasiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Select::make('supplier_id')
                    ->relationship('supplier', 'name')
                    ->required(),
                \Filament\Forms\Components\Select::make('barang_id')
                    ->relationship('barang', 'name')
                    ->required(),
                \Filament\Forms\Components\DatePicker::make('tanggal')
                    ->default(now())
                    ->required(),
                \Filament\Forms\Components\Select::make('jenis_transaksi')
                    ->options([
                        'barang_masuk' => 'Barang Masuk',
                        'barang_retur' => 'Barang Retur (Keluar)',
                    ])
                    ->required(),
                \Filament\Forms\Components\TextInput::make('qty_bal')
                    ->label('Qty (Bal)')
                    ->numeric()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (callable $set, callable $get) {
                        $qty = (int) $get('qty_bal');
                        $price = (int) str_replace('.', '', $get('harga_satuan') ?? '0');
                        $set('total_hutang', $qty * $price);
                    }),
                \Filament\Forms\Components\TextInput::make('harga_satuan')
                    ->label('Harga Satuan')
                    ->prefix('Rp')
                    ->required()
                    ->mask(RawJs::make(<<<'JS'
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
                    JS))
                    ->stripCharacters('.')
                    ->live()
                    ->afterStateUpdated(function (callable $set, callable $get) {
                        $qty = (int) $get('qty_bal');
                        $price = (int) str_replace('.', '', $get('harga_satuan') ?? '0');
                        $set('total_hutang', $qty * $price);
                    }),
                \Filament\Forms\Components\TextInput::make('total_hutang')
                    ->label('Total Hutang')
                    ->prefix('Rp')
                    ->default(0)
                    ->mask(RawJs::make(<<<'JS'
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
                    JS))
                    ->stripCharacters('.'),
                \Filament\Forms\Components\Textarea::make('keterangan')
                    ->columnSpanFull(),
            ]);
    }
}
