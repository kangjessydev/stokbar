<?php

namespace App\Filament\Resources\PiutangPayments;

use App\Filament\Resources\PiutangPayments\Pages\CreatePiutangPayment;
use App\Filament\Resources\PiutangPayments\Pages\EditPiutangPayment;
use App\Filament\Resources\PiutangPayments\Pages\ListPiutangPayments;
use App\Filament\Resources\PiutangPayments\Schemas\PiutangPaymentForm;
use App\Filament\Resources\PiutangPayments\Tables\PiutangPaymentsTable;
use App\Models\PiutangPayment;
use BackedEnum;
use Filament\Resources\Resource;
use UnitEnum;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PiutangPaymentResource extends Resource
{
    protected static ?string $model = PiutangPayment::class;

    protected static string | UnitEnum | null $navigationGroup = 'Keuangan & Tagihan';

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-banknotes';

    public static function form(Schema $schema): Schema
    {
        return PiutangPaymentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PiutangPaymentsTable::configure($table);
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
            'index' => ListPiutangPayments::route('/'),
            'create' => CreatePiutangPayment::route('/create'),
            'edit' => EditPiutangPayment::route('/{record}/edit'),
        ];
    }
}
