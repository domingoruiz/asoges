<?php

namespace App\Resources\CategoriaInventarios\Pages;

use Filament\Actions\CreateAction;
use App\Resources\CategoriaInventarios\CategoriaInventarioResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListCategoriaInventario extends ListRecords
{
    protected static string $resource = CategoriaInventarioResource::class;

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