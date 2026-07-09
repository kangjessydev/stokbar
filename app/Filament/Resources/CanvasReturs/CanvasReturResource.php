<?php

namespace App\Filament\Resources\CanvasReturs;

use App\Filament\Resources\CanvasReturs\Pages\CreateCanvasRetur;
use App\Filament\Resources\CanvasReturs\Pages\EditCanvasRetur;
use App\Filament\Resources\CanvasReturs\Pages\ListCanvasReturs;
use App\Filament\Resources\CanvasReturs\Schemas\CanvasReturForm;
use App\Filament\Resources\CanvasReturs\Tables\CanvasRetursTable;
use App\Models\CanvasRetur;
use BackedEnum;
use Filament\Resources\Resource;
use UnitEnum;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CanvasReturResource extends Resource
{
    protected static ?string $model = CanvasRetur::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-arrow-down-tray';
    protected static string | UnitEnum | null $navigationGroup = 'Penjualan & Distribusi';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return CanvasReturForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CanvasRetursTable::configure($table);
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
            'index' => ListCanvasReturs::route('/'),
            'create' => CreateCanvasRetur::route('/create'),
            'edit' => EditCanvasRetur::route('/{record}/edit'),
        ];
    }
}
