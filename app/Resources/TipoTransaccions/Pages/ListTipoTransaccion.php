<?php

namespace App\Resources\TipoTransaccions\Pages;

use Filament\Actions\CreateAction;
use App\Resources\TipoTransaccions\TipoTransaccionResource;
use Filament\Resources\Pages\ListRecords;

class ListTipoTransaccion extends ListRecords
{
    protected static string $resource = TipoTransaccionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}