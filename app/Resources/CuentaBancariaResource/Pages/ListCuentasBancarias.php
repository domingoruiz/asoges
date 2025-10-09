<?php

namespace App\Resources\CuentaBancariaResource\Pages;

use App\Resources\CuentaBancariaResource;
use Filament\Resources\Pages\ListRecords;

class ListCuentasBancarias extends ListRecords
{
    protected static string $resource = CuentaBancariaResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}