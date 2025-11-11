<?php

namespace App\Resources\LibroSocios\Pages;

use Filament\Actions\CreateAction;
use App\Resources\LibroSocios\LibroSociosResource;
use Filament\Resources\Pages\ListRecords;

class ListLibroSocios extends ListRecords
{
    protected static string $resource = LibroSociosResource::class;

    protected function getHeaderActions(): array
    {
        return [ CreateAction::make() ];
    }
}