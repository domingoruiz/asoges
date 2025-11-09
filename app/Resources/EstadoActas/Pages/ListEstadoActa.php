<?php

namespace App\Resources\EstadoActas\Pages;

use Filament\Actions\CreateAction;
use App\Resources\EstadoActas\EstadoActaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEstadoActa extends ListRecords
{
    protected static string $resource = EstadoActaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}