<?php

namespace App\Resources\ContinentResource\Pages;

use App\Resources\ContinentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListContinents extends ListRecords
{
    protected static string $resource = ContinentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
