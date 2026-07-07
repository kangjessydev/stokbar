<?php

namespace App\Filament\Resources\CanvasMuatans\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CanvasMuatanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('sales_car_id')
                    ->label('Mobil Sales')
                    ->relationship('salesCar', 'name')
                    ->required()
                    ->disabled(fn ($record) => $record && $record->status === 'confirmed'),
                Select::make('barang_id')
                    ->label('Barang Jadi')
                    ->relationship('barang', 'name')
                    ->required()
                    ->disabled(fn ($record) => $record && $record->status === 'confirmed'),
                TextInput::make('qty_loaded')
                    ->label('Jumlah Dimuat (Pcs)')
                    ->numeric()
                    ->required()
                    ->disabled(fn ($record) => $record && $record->status === 'confirmed'),
                DatePicker::make('tanggal')
                    ->label('Tanggal Pengiriman')
                    ->default(now())
                    ->required()
                    ->disabled(fn ($record) => $record && $record->status === 'confirmed'),
                Select::make('status')
                    ->label('Status Muatan')
                    ->options([
                        'pending' => 'Pending (Mengantri)',
                        'confirmed' => 'Confirmed (Barang Sudah Di Mobil & Potong Stok Gudang)',
                    ])
                    ->default('pending')
                    ->required()
                    ->disabled(fn ($record) => $record && $record->status === 'confirmed'), // Kunci jika sudah terkonfirmasi
            ]);
    }
}
