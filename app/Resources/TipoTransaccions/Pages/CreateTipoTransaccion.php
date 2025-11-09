<?php

namespace App\Resources\TipoTransaccions\Pages;

use App\Resources\TipoTransaccions\TipoTransaccionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTipoTransaccion extends CreateRecord
{
    protected static string $resource = TipoTransaccionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['aso_id'] = $data['aso_id'] ?? session('aso_actual');
        return $data;
    }
}