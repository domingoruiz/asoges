<?php

namespace App\Resources\CuentaBancariaResource\Pages;

use App\Resources\CuentaBancariaResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCuentaBancaria extends CreateRecord
{
    protected static string $resource = CuentaBancariaResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['aso_id'] = $data['aso_id'] ?? session('aso_actual');
        return $data;
    }
}
