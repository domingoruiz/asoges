<?php

namespace App\Resources\LibroContabilidads\Pages;

use Filament\Actions\CreateAction;
use App\Resources\LibroContabilidads\LibroContabilidadResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLibroContabilidad extends ListRecords
{
    protected static string $resource = LibroContabilidadResource::class;

    protected function getHeaderActions(): array
    {
        return [ CreateAction::make() ];
    }
}