<?php

namespace App\Resources\LibroProyectos\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use App\Resources\LibroProyectos\LibroProyectoResource;

class ListLibroProyectos extends ListRecords
{
    protected static string $resource = LibroProyectoResource::class;

    protected function getHeaderActions(): array
    {
        return [ CreateAction::make() ];
    }
}