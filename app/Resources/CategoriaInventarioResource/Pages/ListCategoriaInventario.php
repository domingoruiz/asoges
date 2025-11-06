<?php

namespace App\Resources\CategoriaInventarioResource\Pages;

use App\Resources\CategoriaInventarioResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListCategoriaInventario extends ListRecords
{
    protected static string $resource = CategoriaInventarioResource::class;

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