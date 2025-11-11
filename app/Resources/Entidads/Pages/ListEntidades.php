<?php

namespace App\Resources\Entidads\Pages;

use Filament\Actions\CreateAction;
use App\Resources\Entidads\EntidadResource;
use Filament\Resources\Pages\ListRecords;

class ListEntidades extends ListRecords
{
    protected static string $resource = EntidadResource::class;

    protected function getHeaderActions(): array
    {
        return [ CreateAction::make() ];
    }
}