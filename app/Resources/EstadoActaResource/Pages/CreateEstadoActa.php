<?php

namespace App\Resources\EstadoActaResource\Pages;

use App\Resources\EstadoActaResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEstadoActa extends CreateRecord
{
    protected static string $resource = EstadoActaResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['aso_id'] = $data['aso_id'] ?? session('aso_actual');
        return $data;
    }
}