<?php

namespace App\Resources\LibroSociosResource\Pages;

use App\Resources\LibroSociosResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLibroSocios extends ListRecords
{
    protected static string $resource = LibroSociosResource::class;

    protected function getHeaderActions(): array
    {
        return [ Actions\CreateAction::make() ];
    }
}