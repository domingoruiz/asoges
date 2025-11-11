<?php

namespace App\Resources\Continents\Pages;

use Filament\Actions\CreateAction;
use App\Resources\Continents\ContinentResource;
use Filament\Resources\Pages\ListRecords;

class ListContinents extends ListRecords
{
    protected static string $resource = ContinentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
