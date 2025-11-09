<?php

namespace App\Resources\Ubicacions\Pages;

use Filament\Actions\CreateAction;
use App\Resources\Ubicacions\UbicacionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListUbicacion extends ListRecords
{
    protected static string $resource = UbicacionResource::class;

    protected function getHeaderActions(): array
    {
        return [ CreateAction::make() ];
    }

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()
            ->whereNull('categoria_padre_id');
    }
}