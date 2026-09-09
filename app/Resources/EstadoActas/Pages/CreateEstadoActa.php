<?php

namespace App\Resources\EstadoActas\Pages;

use App\Resources\EstadoActas\EstadoActaResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEstadoActa extends CreateRecord
{
    protected static string $resource = EstadoActaResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['aso_id'] = session('aso_actual');
        return $data;
    }
}