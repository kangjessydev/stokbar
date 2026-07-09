<?php

namespace App\Filament\Resources\CanvasMuatans;

use App\Filament\Resources\CanvasMuatans\Pages\CreateCanvasMuatan;
use App\Filament\Resources\CanvasMuatans\Pages\EditCanvasMuatan;
use App\Filament\Resources\CanvasMuatans\Pages\ListCanvasMuatans;
use App\Filament\Resources\CanvasMuatans\Schemas\CanvasMuatanForm;
use App\Filament\Resources\CanvasMuatans\Tables\CanvasMuatansTable;
use App\Models\CanvasMuatan;
use BackedEnum;
use Filament\Resources\Resource;
use UnitEnum;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CanvasMuatanResource extends Resource
{
    protected static ?string $model = CanvasMuatan::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-arrow-up-tray';
    protected static string | UnitEnum | null $navigationGroup = 'Penjualan & Distribusi';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return CanvasMuatanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CanvasMuatansTable::configure($table);
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
            'index' => ListCanvasMuatans::route('/'),
            'create' => CreateCanvasMuatan::route('/create'),
            'edit' => EditCanvasMuatan::route('/{record}/edit'),
        ];
    }
}
