<?php

namespace App\Resources\TipoActas\Pages;

use App\Resources\TipoActas\TipoActaResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTipoActa extends CreateRecord
{
    protected static string $resource = TipoActaResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['aso_id'] = $data['aso_id'] ?? session('aso_actual');
        return $data;
    }
}