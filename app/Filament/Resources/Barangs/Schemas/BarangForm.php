<?php

namespace App\Filament\Resources\Barangs\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;

class BarangForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('gudang_id')
                    ->label('Gudang Penyimpanan')
                    ->relationship('gudang', 'name', fn ($query) => $query->where('type', 'barang_jadi'))
                    ->required(),
                TextInput::make('name')
                    ->label('Nama Barang Jadi')
                    ->required()
                    ->maxLength(255),
                TextInput::make('stock')
                    ->label('Stok Saat Ini')
                    ->numeric()
                    ->default(0)
                    ->required(),
                Select::make('unit')
                    ->label('Satuan')
                    ->options([
                        'pcs' => 'Pcs (Pieces)',
                        'box' => 'Box / Dus',
                        'pack' => 'Pack',
                    ])
                    ->default('pcs')
                    ->required(),
                TextInput::make('price')
                    ->label('Harga Jual (Rp)')
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
                    ->default(0)
                    ->required(),
            ]);
    }
}
