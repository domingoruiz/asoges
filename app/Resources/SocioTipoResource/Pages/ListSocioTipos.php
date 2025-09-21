<?php

namespace App\Resources\SocioTipoResource\Pages;

use App\Resources\SocioTipoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSocioTipos extends ListRecords
{
    protected static string $resource = SocioTipoResource::class;

    protected function getHeaderActions(): array
    {
        return [ Actions\CreateAction::make() ];
    }
}
