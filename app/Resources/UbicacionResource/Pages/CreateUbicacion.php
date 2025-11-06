<?php

namespace App\Resources\UbicacionResource\Pages;

use App\Resources\UbicacionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUbicacion extends CreateRecord
{
    protected static string $resource = UbicacionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['aso_id'] = $data['aso_id'] ?? session('aso_actual');
        $data['categoria_padre_id'] = null;
        return $data;
    }
}