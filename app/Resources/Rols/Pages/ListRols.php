<?php

namespace App\Resources\Rols\Pages;

use Filament\Actions\CreateAction;
use App\Resources\Rols\RolResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRols extends ListRecords
{
    protected static string $resource = RolResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
