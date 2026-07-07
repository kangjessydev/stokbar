<?php

namespace App\Filament\Resources\Barangs\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

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
                    ->numeric()
                    ->prefix('Rp')
                    ->default(0.00)
                    ->required(),
            ]);
    }
}
