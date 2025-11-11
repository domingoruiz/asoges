<?php

namespace App\Resources\TipoDocumentos\Pages;

use Filament\Actions\CreateAction;
use App\Resources\TipoDocumentos\TipoDocumentoResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListTipoDocumento extends ListRecords
{
    protected static string $resource = TipoDocumentoResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()
            ->whereNull('categoria_padre_id');
    }
}