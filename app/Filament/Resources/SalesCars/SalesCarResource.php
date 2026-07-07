<?php

namespace App\Filament\Resources\SalesCars;

use App\Filament\Resources\SalesCars\Pages\CreateSalesCar;
use App\Filament\Resources\SalesCars\Pages\EditSalesCar;
use App\Filament\Resources\SalesCars\Pages\ListSalesCars;
use App\Filament\Resources\SalesCars\Schemas\SalesCarForm;
use App\Filament\Resources\SalesCars\Tables\SalesCarsTable;
use App\Models\SalesCar;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SalesCarResource extends Resource
{
    protected static ?string $model = SalesCar::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-truck';
    protected static string|\UnitEnum|null $navigationGroup = 'Data Master';
    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return SalesCarForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SalesCarsTable::configure($table);
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
            'index' => ListSalesCars::route('/'),
            'create' => CreateSalesCar::route('/create'),
            'edit' => EditSalesCar::route('/{record}/edit'),
        ];
    }
}
