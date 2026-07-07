<?php

namespace App\Filament\Resources\AuditLogs\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AuditLogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label('Dilakukan Oleh')
                    ->relationship('user', 'name')
                    ->disabled(),
                TextInput::make('action')
                    ->label('Operasi / Aksi')
                    ->disabled(),
                TextInput::make('model_type')
                    ->label('Nama Model (Data)')
                    ->disabled(),
                TextInput::make('model_id')
                    ->label('ID Record')
                    ->disabled(),
                TextInput::make('ip_address')
                    ->label('IP Address')
                    ->disabled(),
                DateTimePicker::make('created_at')
                    ->label('Waktu Kejadian')
                    ->disabled(),
                Textarea::make('old_values')
                    ->label('Data Lama (Sebelum Perubahan)')
                    ->formatStateUsing(fn ($state) => is_array($state) ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : $state)
                    ->rows(8)
                    ->columnSpanFull()
                    ->disabled(),
                Textarea::make('new_values')
                    ->label('Data Baru (Setelah Perubahan)')
                    ->formatStateUsing(fn ($state) => is_array($state) ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : $state)
                    ->rows(8)
                    ->columnSpanFull()
                    ->disabled(),
                Textarea::make('user_agent')
                    ->label('Detail Perangkat (User Agent)')
                    ->rows(2)
                    ->columnSpanFull()
                    ->disabled(),
            ]);
    }
}
