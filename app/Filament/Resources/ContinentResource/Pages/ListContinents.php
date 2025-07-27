<?php

namespace App\Filament\Resources\ContinentResource\Pages;

use App\Filament\Resources\ContinentResource;
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
