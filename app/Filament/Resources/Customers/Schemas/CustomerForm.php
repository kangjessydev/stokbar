<?php

namespace App\Filament\Resources\Customers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;

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
                    ->prefix('Rp')
                    ->mask(\Filament\Support\RawJs::make(<<<'JS'
                        $input ? (function() {
                            let clean = $input.replace(/\D/g, '');
                            let len = clean.length;
                            if (len <= 3) return '9'.repeat(len);
                            let remainder = len % 3;
                            let parts = [];
                            if (remainder > 0) parts.push('9'.repeat(remainder));
                            let groups = Math.floor(len / 3);
                            for (let i = 0; i < groups; i++) parts.push('999');
                            return parts.join('.');
                        })() : ''
                    JS
                    ))
                    ->stripCharacters('.')
                    ->default(1000000)
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
