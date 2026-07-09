<?php

namespace App\Filament\Resources\SupplierMutasis;

use App\Filament\Resources\SupplierMutasis\Pages\CreateSupplierMutasi;
use App\Filament\Resources\SupplierMutasis\Pages\EditSupplierMutasi;
use App\Filament\Resources\SupplierMutasis\Pages\ListSupplierMutasis;
use App\Filament\Resources\SupplierMutasis\Schemas\SupplierMutasiForm;
use App\Filament\Resources\SupplierMutasis\Tables\SupplierMutasisTable;
use App\Models\SupplierMutasi;
use BackedEnum;
use Filament\Resources\Resource;
use UnitEnum;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SupplierMutasiResource extends Resource
{
    protected static ?string $model = SupplierMutasi::class;

    protected static string | UnitEnum | null $navigationGroup = 'Pembelian';

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-inbox-arrow-down';

    public static function form(Schema $schema): Schema
    {
        return SupplierMutasiForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SupplierMutasisTable::configure($table);
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
            'index' => ListSupplierMutasis::route('/'),
            'create' => CreateSupplierMutasi::route('/create'),
            'edit' => EditSupplierMutasi::route('/{record}/edit'),
        ];
    }
}
