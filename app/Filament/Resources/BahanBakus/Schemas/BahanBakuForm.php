<?php

namespace App\Filament\Resources\BahanBakus\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BahanBakuForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('gudang_id')
                    ->label('Gudang Penyimpanan')
                    ->relationship('gudang', 'name', fn ($query) => $query->where('type', 'bahan_baku'))
                    ->required(),
                TextInput::make('name')
                    ->label('Nama Bahan Baku')
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
                        'kg' => 'Kilogram (kg)',
                        'gr' => 'Gram (gr)',
                        'liter' => 'Liter (L)',
                        'ml' => 'Mililiter (ml)',
                        'pcs' => 'Pcs',
                    ])
                    ->required(),
                TextInput::make('safety_stock')
                    ->label('Batas Aman Stok (Safety Stock)')
                    ->numeric()
                    ->default(0)
                    ->required(),
            ]);
    }
}
