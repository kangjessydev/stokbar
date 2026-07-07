<?php

namespace App\Filament\Resources\CanvasReturs\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CanvasReturForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('sales_car_id')
                    ->label('Mobil Sales')
                    ->relationship('salesCar', 'name')
                    ->required(),
                Select::make('barang_id')
                    ->label('Barang Jadi')
                    ->relationship('barang', 'name')
                    ->required(),
                TextInput::make('qty_returned')
                    ->label('Jumlah Retur Sisa (Pcs)')
                    ->numeric()
                    ->helperText('Jumlah barang sisa yang dikembalikan dari mobil sales ke Gudang Utama.')
                    ->required(),
                DatePicker::make('tanggal')
                    ->label('Tanggal Retur')
                    ->default(now())
                    ->required(),
            ]);
    }
}
