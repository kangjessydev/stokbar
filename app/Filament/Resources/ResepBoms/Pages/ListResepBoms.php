<?php

namespace App\Filament\Resources\ResepBoms\Pages;

use App\Filament\Resources\ResepBoms\ResepBomResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListResepBoms extends ListRecords
{
    protected static string $resource = ResepBomResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
