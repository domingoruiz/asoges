<?php

namespace App\Resources\CuentaBancarias\Pages;

use Filament\Actions\CreateAction;
use App\Resources\CuentaBancarias\CuentaBancariaResource;
use Filament\Resources\Pages\ListRecords;

class ListCuentasBancarias extends ListRecords
{
    protected static string $resource = CuentaBancariaResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}