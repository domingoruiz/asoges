<?php

namespace App\Resources\LibroActas\Pages;

use Filament\Actions\CreateAction;
use App\Resources\LibroActas\LibroActasResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLibroActas extends ListRecords
{
    protected static string $resource = LibroActasResource::class;

    protected function getHeaderActions(): array
    {
        return [ CreateAction::make() ];
    }
}