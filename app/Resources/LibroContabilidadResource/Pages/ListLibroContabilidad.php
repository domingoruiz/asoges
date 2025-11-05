<?php

namespace App\Resources\LibroContabilidadResource\Pages;

use App\Resources\LibroContabilidadResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLibroContabilidad extends ListRecords
{
    protected static string $resource = LibroContabilidadResource::class;

    protected function getHeaderActions(): array
    {
        return [ Actions\CreateAction::make() ];
    }
}