<?php

namespace App\Resources\SocioTipos\Pages;

use Filament\Actions\CreateAction;
use App\Resources\SocioTipos\SocioTipoResource;
use Filament\Resources\Pages\ListRecords;

class ListSocioTipos extends ListRecords
{
    protected static string $resource = SocioTipoResource::class;

    protected function getHeaderActions(): array
    {
        return [ CreateAction::make() ];
    }
}
