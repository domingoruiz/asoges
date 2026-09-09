<?php

namespace App\Resources\LibroInteraccions\Pages;

use App\Resources\LibroInteraccions\LibroInteraccionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLibroInteracciones extends ListRecords
{
    protected static string $resource = LibroInteraccionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
