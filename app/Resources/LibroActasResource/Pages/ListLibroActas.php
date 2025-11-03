<?php

namespace App\Resources\LibroActasResource\Pages;

use App\Resources\LibroActasResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLibroActas extends ListRecords
{
    protected static string $resource = LibroActasResource::class;

    protected function getHeaderActions(): array
    {
        return [ Actions\CreateAction::make() ];
    }
}