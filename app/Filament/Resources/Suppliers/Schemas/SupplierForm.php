<?php

namespace App\Filament\Resources\Suppliers\Schemas;

use Filament\Schemas\Schema;

class SupplierForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\TextInput::make('name')
                    ->label('Nama Supplier')
                    ->required()
                    ->maxLength(255),
                \Filament\Forms\Components\TextInput::make('phone')
                    ->label('Telepon')
                    ->tel()
                    ->maxLength(255),
                \Filament\Forms\Components\Textarea::make('address')
                    ->label('Alamat')
                    ->columnSpanFull(),
            ]);
    }
}
