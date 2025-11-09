<?php

namespace App\Resources\Asos\Pages;

use Filament\Actions\CreateAction;
use App\Resources\Asos\AsoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAsos extends ListRecords
{
    protected static string $resource = AsoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
