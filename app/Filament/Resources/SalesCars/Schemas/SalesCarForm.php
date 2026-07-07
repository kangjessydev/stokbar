<?php

namespace App\Filament\Resources\SalesCars\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SalesCarForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Mobil / Identitas')
                    ->placeholder('Misal: Mobil Grand Max L-300')
                    ->required()
                    ->maxLength(255),
                TextInput::make('driver_name')
                    ->label('Nama Driver')
                    ->required()
                    ->maxLength(255),
                Select::make('user_id')
                    ->label('Akun Sales (User)')
                    ->relationship('user', 'name')
                    ->helperText('Pilih akun user yang memegang mobil ini untuk berjualan.')
                    ->searchable()
                    ->preload()
                    ->nullable(),
            ]);
    }
}
