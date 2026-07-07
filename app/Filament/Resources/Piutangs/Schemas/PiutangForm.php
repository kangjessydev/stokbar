<?php

namespace App\Filament\Resources\Piutangs\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

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
                    ->numeric()
                    ->prefix('Rp')
                    ->readOnly(),
                TextInput::make('paid_amount')
                    ->label('Nominal Sudah Terbayar (Rp)')
                    ->numeric()
                    ->prefix('Rp')
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
                            ->numeric()
                            ->prefix('Rp')
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
