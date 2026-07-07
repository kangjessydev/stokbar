<?php

namespace App\Filament\Resources\Produksis\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProduksiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('kode_produksi')
                    ->label('Kode Produksi')
                    ->default(fn () => 'PROD-' . date('YmdHis'))
                    ->required()
                    ->readOnly()
                    ->unique(ignoreRecord: true),
                DatePicker::make('tanggal')
                    ->label('Tanggal Produksi')
                    ->default(now())
                    ->required(),
                Select::make('status')
                    ->label('Status Produksi')
                    ->options([
                        'pending' => 'Pending (Dalam Proses)',
                        'selesai' => 'Selesai (Potong Stok Bahan & Tambah Barang Jadi)',
                        'batal' => 'Batal',
                    ])
                    ->default('pending')
                    ->required()
                    ->disabled(fn ($record) => $record && $record->status === 'selesai'), // Kunci jika sudah selesai
                
                Repeater::make('produksiItems')
                    ->relationship('produksiItems')
                    ->label('Daftar Barang yang Diproduksi')
                    ->schema([
                        Select::make('barang_id')
                            ->label('Barang Jadi')
                            ->relationship('barang', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('qty_produced')
                            ->label('Jumlah Hasil Produksi (Pcs)')
                            ->numeric()
                            ->required(),
                    ])
                    ->columns(2)
                    ->defaultItems(1)
                    ->required()
                    ->disabled(fn ($record) => $record && $record->status === 'selesai'), // Kunci jika sudah selesai
            ]);
    }
}
