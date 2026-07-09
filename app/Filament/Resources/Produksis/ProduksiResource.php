<?php

namespace App\Filament\Resources\Produksis;

use App\Filament\Resources\Produksis\Pages\CreateProduksi;
use App\Filament\Resources\Produksis\Pages\EditProduksi;
use App\Filament\Resources\Produksis\Pages\ListProduksis;
use App\Filament\Resources\Produksis\Schemas\ProduksiForm;
use App\Filament\Resources\Produksis\Tables\ProduksisTable;
use App\Models\Produksi;
use BackedEnum;
use Filament\Resources\Resource;
use UnitEnum;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ProduksiResource extends Resource
{
    protected static ?string $model = Produksi::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static string | UnitEnum | null $navigationGroup = 'Produksi';
    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'kode_produksi';

    public static function form(Schema $schema): Schema
    {
        return ProduksiForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProduksisTable::configure($table);
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
            'index' => ListProduksis::route('/'),
            'create' => CreateProduksi::route('/create'),
            'edit' => EditProduksi::route('/{record}/edit'),
        ];
    }
}
