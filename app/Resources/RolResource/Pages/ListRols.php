<?php

namespace App\Resources\RolResource\Pages;

use App\Resources\RolResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRols extends ListRecords
{
    protected static string $resource = RolResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
