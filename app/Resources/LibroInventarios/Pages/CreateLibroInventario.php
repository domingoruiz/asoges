<?php

namespace App\Resources\LibroInventarios\Pages;

use App\Resources\LibroInventarios\LibroInventarioResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLibroInventario extends CreateRecord
{
    protected static string $resource = LibroInventarioResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['aso_id'] = session('aso_actual');
        return $data;
    }
}