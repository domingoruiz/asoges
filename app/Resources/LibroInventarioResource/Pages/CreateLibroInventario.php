<?php

namespace App\Resources\LibroInventarioResource\Pages;

use App\Resources\LibroInventarioResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLibroInventario extends CreateRecord
{
    protected static string $resource = LibroInventarioResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['aso_id'] = $data['aso_id'] ?? session('aso_actual');
        return $data;
    }
}