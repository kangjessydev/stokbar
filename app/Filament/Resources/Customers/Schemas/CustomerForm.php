<?php

namespace App\Filament\Resources\Customers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Customer / Toko')
                    ->required()
                    ->maxLength(255),
                TextInput::make('market')
                    ->label('Pasar / Wilayah')
                    ->placeholder('Misal: Pasar Wage, Pasar manis')
                    ->required()
                    ->maxLength(255),
                TextInput::make('phone')
                    ->label('No. Telepon / WhatsApp')
                    ->tel()
                    ->maxLength(255),
                TextInput::make('credit_limit')
                    ->label('Limit Kredit (Rp)')
                    ->numeric()
                    ->prefix('Rp')
                    ->default(1000000.00)
                    ->helperText('Batas maksimal piutang yang diperbolehkan untuk toko ini.'),
                TextInput::make('credit_period')
                    ->label('Tempo Kredit (Hari)')
                    ->numeric()
                    ->default(14)
                    ->suffix('Hari')
                    ->helperText('Jangka waktu pelunasan nota kredit.'),
            ]);
    }
}
