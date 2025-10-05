<?php

namespace App\Resources\EjercicioResource\Pages;

use App\Resources\EjercicioResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEjercicios extends ListRecords
{
    protected static string $resource = EjercicioResource::class;

    protected function getHeaderActions(): array
    {
        return [ Actions\CreateAction::make() ];
    }
}