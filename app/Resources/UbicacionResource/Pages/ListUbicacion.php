<?php

namespace App\Resources\UbicacionResource\Pages;

use App\Resources\UbicacionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListUbicacion extends ListRecords
{
    protected static string $resource = UbicacionResource::class;

    protected function getHeaderActions(): array
    {
        return [ Actions\CreateAction::make() ];
    }

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()
            ->whereNull('categoria_padre_id');
    }
}