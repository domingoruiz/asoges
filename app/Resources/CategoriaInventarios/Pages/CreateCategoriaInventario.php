<?php

namespace App\Resources\CategoriaInventarios\Pages;

use App\Resources\CategoriaInventarios\CategoriaInventarioResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCategoriaInventario extends CreateRecord
{
    protected static string $resource = CategoriaInventarioResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['aso_id'] = session('aso_actual');
        $data['categoria_padre_id'] = null;
        return $data;
    }
}