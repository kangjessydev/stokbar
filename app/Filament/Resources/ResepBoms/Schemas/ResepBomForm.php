<?php

namespace App\Filament\Resources\ResepBoms\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ResepBomForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('barang_id')
                    ->label('Barang Jadi')
                    ->relationship('barang', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('bahan_baku_id')
                    ->label('Bahan Baku Utama')
                    ->relationship('bahanBaku', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('qty_needed')
                    ->label('Kebutuhan Bahan Baku')
                    ->numeric()
                    ->helperText('Jumlah bahan baku yang digunakan untuk memproduksi 1 unit barang jadi.')
                    ->required(),
            ]);
    }
}
