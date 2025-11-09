<?php

namespace App\Resources\GestorDocumentals\Pages;

use Filament\Actions\CreateAction;
use App\Resources\GestorDocumentals\GestorDocumentalResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGestorDocumental extends ListRecords
{
    protected static string $resource = GestorDocumentalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}