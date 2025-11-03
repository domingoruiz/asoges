<?php

namespace App\Resources\TipoActaResource\Pages;

use App\Resources\TipoActaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTipoActa extends ListRecords
{
    protected static string $resource = TipoActaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}