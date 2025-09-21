<?php

namespace App\Filament\Resources\SocioTipoResource\Pages;

use App\Filament\Resources\SocioTipoResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListSocioTipos extends ListRecords
{
    protected static string $resource = SocioTipoResource::class;

    protected function getHeaderActions(): array
    {
        return [ Actions\CreateAction::make() ];
    }
}
