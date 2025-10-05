<?php

namespace App\Resources\PaiResource\Pages;

use App\Resources\PaiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPais extends ListRecords
{
    protected static string $resource = PaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
