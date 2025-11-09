<?php

namespace App\Resources\TipoActas\Pages;

use Filament\Actions\CreateAction;
use App\Resources\TipoActas\TipoActaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTipoActa extends ListRecords
{
    protected static string $resource = TipoActaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}