<?php

namespace App\Resources\CategoriaContables\Pages;

use Filament\Actions\CreateAction;
use App\Resources\CategoriaContables\CategoriaContableResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListCategoriaContable extends ListRecords
{
    protected static string $resource = CategoriaContableResource::class;

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