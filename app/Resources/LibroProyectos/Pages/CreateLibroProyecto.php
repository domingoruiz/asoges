<?php

namespace App\Resources\LibroProyectos\Pages;

use Filament\Resources\Pages\CreateRecord;
use App\Resources\LibroProyectos\LibroProyectoResource;

class CreateLibroProyecto extends CreateRecord
{
    protected static string $resource = LibroProyectoResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['aso_id'] = $data['aso_id'] ?? session('aso_actual');
        return $data;
    }
}