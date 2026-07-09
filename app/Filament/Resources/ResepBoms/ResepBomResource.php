<?php

namespace App\Filament\Resources\ResepBoms;

use App\Filament\Resources\ResepBoms\Pages\CreateResepBom;
use App\Filament\Resources\ResepBoms\Pages\EditResepBom;
use App\Filament\Resources\ResepBoms\Pages\ListResepBoms;
use App\Filament\Resources\ResepBoms\Schemas\ResepBomForm;
use App\Filament\Resources\ResepBoms\Tables\ResepBomsTable;
use App\Models\ResepBom;
use BackedEnum;
use Filament\Resources\Resource;
use UnitEnum;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ResepBomResource extends Resource
{
    protected static ?string $model = ResepBom::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static string | UnitEnum | null $navigationGroup = 'Master Data';
    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return ResepBomForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ResepBomsTable::configure($table);
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
            'index' => ListResepBoms::route('/'),
            'create' => CreateResepBom::route('/create'),
            'edit' => EditResepBom::route('/{record}/edit'),
        ];
    }
}
