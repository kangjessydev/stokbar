<?php

namespace App\Filament\Resources\Piutangs\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;

class PiutangForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('invoice_id')
                    ->label('Nota Invoice Relasi')
                    ->relationship('invoice', 'no_invoice')
                    ->disabled()
                    ->required(),
                TextInput::make('amount')
                    ->label('Total Hutang Customer (Rp)')
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
                    ->readOnly(),
                TextInput::make('paid_amount')
                    ->label('Nominal Sudah Terbayar (Rp)')
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
                    ->readOnly(),
                Select::make('status')
                    ->label('Status Piutang')
                    ->options([
                        'belum_lunas' => 'Belum Lunas',
                        'lunas' => 'Lunas',
                    ])
                    ->disabled()
                    ->required(),
                DatePicker::make('due_date')
                    ->label('Tanggal Jatuh Tempo')
                    ->required(),
                
                Repeater::make('piutangPayments')
                    ->relationship('piutangPayments')
                    ->label('Riwayat Cicilan & Pelunasan Piutang')
                    ->schema([
                        TextInput::make('amount_paid')
                            ->label('Nominal Bayar (Rp)')
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
                            ->required(),
                        DatePicker::make('payment_date')
                            ->label('Tanggal Bayar')
                            ->default(now())
                            ->required(),
                        Select::make('payment_method')
                            ->label('Metode Bayar')
                            ->options([
                                'cash' => 'Tunai / Cash',
                                'transfer' => 'Transfer Bank',
                             ])
                            ->default('cash')
                            ->required(),
                        TextInput::make('reference_no')
                            ->label('Nomor Slip / Ref')
                            ->maxLength(255),
                    ])
                    ->columns(4)
                    ->defaultItems(0),
            ]);
    }
}
