<?php

namespace App\Filament\Resources\CanvasReturs\Pages;

use App\Filament\Resources\CanvasReturs\CanvasReturResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCanvasReturs extends ListRecords
{
    protected static string $resource = CanvasReturResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
