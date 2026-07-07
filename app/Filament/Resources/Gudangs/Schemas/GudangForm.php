<?php

namespace App\Filament\Resources\Gudangs\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class GudangForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Gudang')
                    ->required()
                    ->maxLength(255),
                Select::make('type')
                    ->label('Tipe Gudang')
                    ->options([
                        'bahan_baku' => 'Gudang Bahan Baku',
                        'barang_jadi' => 'Gudang Barang Jadi',
                    ])
                    ->required(),
            ]);
    }
}
