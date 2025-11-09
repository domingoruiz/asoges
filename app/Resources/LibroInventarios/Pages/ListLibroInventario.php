<?php

namespace App\Resources\LibroInventarios\Pages;

use Filament\Actions\CreateAction;
use App\Resources\LibroInventarios\LibroInventarioResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLibroInventario extends ListRecords
{
    protected static string $resource = LibroInventarioResource::class;

    protected function getHeaderActions(): array
    {
        return [ CreateAction::make() ];
    }
}