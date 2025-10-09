<?php

namespace App\Resources\EntidadResource\Pages;

use App\Resources\EntidadResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEntidades extends ListRecords
{
    protected static string $resource = EntidadResource::class;

    protected function getHeaderActions(): array
    {
        return [ Actions\CreateAction::make() ];
    }
}