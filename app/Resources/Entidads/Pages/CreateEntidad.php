<?php

namespace App\Resources\Entidads\Pages;

use App\Resources\Entidads\EntidadResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEntidad extends CreateRecord
{
    protected static string $resource = EntidadResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['aso_id'] = $data['aso_id'] ?? session('aso_actual');
        return $data;
    }
}