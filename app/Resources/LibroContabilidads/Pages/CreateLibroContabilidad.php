<?php

namespace App\Resources\LibroContabilidads\Pages;

use App\Resources\LibroContabilidads\LibroContabilidadResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLibroContabilidad extends CreateRecord
{
    protected static string $resource = LibroContabilidadResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['aso_id'] = $data['aso_id'] ?? session('aso_actual');
        return $data;
    }
}