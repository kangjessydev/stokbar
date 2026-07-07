<?php

namespace App\Filament\Resources\Piutangs;

use App\Filament\Resources\Piutangs\Pages\CreatePiutang;
use App\Filament\Resources\Piutangs\Pages\EditPiutang;
use App\Filament\Resources\Piutangs\Pages\ListPiutangs;
use App\Filament\Resources\Piutangs\Schemas\PiutangForm;
use App\Filament\Resources\Piutangs\Tables\PiutangsTable;
use App\Models\Piutang;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PiutangResource extends Resource
{
    protected static ?string $model = Piutang::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';
    protected static string|\UnitEnum|null $navigationGroup = 'Transaksi & Keuangan';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return PiutangForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PiutangsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPiutangs::route('/'),
            'create' => CreatePiutang::route('/create'),
            'edit' => EditPiutang::route('/{record}/edit'),
        ];
    }
}
