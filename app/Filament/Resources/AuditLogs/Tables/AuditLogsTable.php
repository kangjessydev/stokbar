<?php

namespace App\Filament\Resources\AuditLogs\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AuditLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Dilakukan Oleh')
                    ->searchable()
                    ->sortable()
                    ->placeholder('System / Event Hook'),
                TextColumn::make('action')
                    ->label('Aksi')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'created' => 'TAMBAH',
                        'updated' => 'UBAH',
                        'deleted' => 'HAPUS',
                        default => strtoupper($state),
                    })
                    ->sortable(),
                TextColumn::make('model_type')
                    ->label('Jenis Data')
                    ->formatStateUsing(fn ($state) => class_basename($state))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('model_id')
                    ->label('ID Record')
                    ->sortable(),
                TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Waktu Kejadian')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Detail Log'),
            ])
            ->toolbarActions([
                // Kosongkan dari aksi massal hapus demi integritas data audit trail
            ]);
    }
}
