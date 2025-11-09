<?php

namespace App\Resources\Ejercicios\Pages;

use Filament\Actions\CreateAction;
use App\Resources\Ejercicios\EjercicioResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEjercicios extends ListRecords
{
    protected static string $resource = EjercicioResource::class;

    protected function getHeaderActions(): array
    {
        return [ CreateAction::make() ];
    }
}