<?php

namespace App\Resources\CategoriaContableResource\Pages;

use App\Resources\CategoriaContableResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListCategoriaContable extends ListRecords
{
    protected static string $resource = CategoriaContableResource::class;

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