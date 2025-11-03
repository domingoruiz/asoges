<?php

namespace App\Resources\EstadoActaResource\Pages;

use App\Resources\EstadoActaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEstadoActa extends ListRecords
{
    protected static string $resource = EstadoActaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}