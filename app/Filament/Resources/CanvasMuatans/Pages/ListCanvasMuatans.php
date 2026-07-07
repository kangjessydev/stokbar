<?php

namespace App\Filament\Resources\CanvasMuatans\Pages;

use App\Filament\Resources\CanvasMuatans\CanvasMuatanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCanvasMuatans extends ListRecords
{
    protected static string $resource = CanvasMuatanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
