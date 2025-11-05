<?php

namespace App\Resources\TipoTransaccionResource\Pages;

use App\Resources\TipoTransaccionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTipoTransaccion extends ListRecords
{
    protected static string $resource = TipoTransaccionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}