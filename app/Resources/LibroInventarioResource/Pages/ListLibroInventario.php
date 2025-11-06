<?php

namespace App\Resources\LibroInventarioResource\Pages;

use App\Resources\LibroInventarioResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLibroInventario extends ListRecords
{
    protected static string $resource = LibroInventarioResource::class;

    protected function getHeaderActions(): array
    {
        return [ Actions\CreateAction::make() ];
    }
}