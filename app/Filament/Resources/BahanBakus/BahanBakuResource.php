<?php

namespace App\Filament\Resources\BahanBakus;

use App\Filament\Resources\BahanBakus\Pages\CreateBahanBaku;
use App\Filament\Resources\BahanBakus\Pages\EditBahanBaku;
use App\Filament\Resources\BahanBakus\Pages\ListBahanBakus;
use App\Filament\Resources\BahanBakus\Schemas\BahanBakuForm;
use App\Filament\Resources\BahanBakus\Tables\BahanBakusTable;
use App\Models\BahanBaku;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BahanBakuResource extends Resource
{
    protected static ?string $model = BahanBaku::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cube-transparent';
    protected static string|\UnitEnum|null $navigationGroup = 'Inventori & Manufaktur';
    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return BahanBakuForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BahanBakusTable::configure($table);
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
            'index' => ListBahanBakus::route('/'),
            'create' => CreateBahanBaku::route('/create'),
            'edit' => EditBahanBaku::route('/{record}/edit'),
        ];
    }
}
