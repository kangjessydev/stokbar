<?php

namespace App\Filament\Resources\Hutangs;

use App\Filament\Resources\Hutangs\Pages\CreateHutang;
use App\Filament\Resources\Hutangs\Pages\EditHutang;
use App\Filament\Resources\Hutangs\Pages\ListHutangs;
use App\Filament\Resources\Hutangs\Schemas\HutangForm;
use App\Filament\Resources\Hutangs\Tables\HutangsTable;
use App\Models\Hutang;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class HutangResource extends Resource
{
    protected static ?string $model = Hutang::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';
    protected static string|\UnitEnum|null $navigationGroup = 'Transaksi & Keuangan';
    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return HutangForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HutangsTable::configure($table);
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
            'index' => ListHutangs::route('/'),
            'create' => CreateHutang::route('/create'),
            'edit' => EditHutang::route('/{record}/edit'),
        ];
    }
}
