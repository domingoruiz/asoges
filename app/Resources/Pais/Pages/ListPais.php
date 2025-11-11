<?php

namespace App\Resources\Pais\Pages;

use Filament\Actions\CreateAction;
use App\Resources\Pais\PaiResource;
use Filament\Resources\Pages\ListRecords;

class ListPais extends ListRecords
{
    protected static string $resource = PaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
