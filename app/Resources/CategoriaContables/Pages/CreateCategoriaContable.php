<?php

namespace App\Resources\CategoriaContables\Pages;

use App\Resources\CategoriaContables\CategoriaContableResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCategoriaContable extends CreateRecord
{
    protected static string $resource = CategoriaContableResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['aso_id'] = $data['aso_id'] ?? session('aso_actual');
        $data['categoria_padre_id'] = null;
        return $data;
    }
}