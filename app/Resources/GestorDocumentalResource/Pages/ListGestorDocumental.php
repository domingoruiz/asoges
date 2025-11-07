<?php

namespace App\Resources\GestorDocumentalResource\Pages;

use App\Resources\GestorDocumentalResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGestorDocumental extends ListRecords
{
    protected static string $resource = GestorDocumentalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}