<?php

namespace App\Resources\Tareas\Pages;

use App\Models\Tarea;
use App\Resources\Tareas\TareaResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTarea extends CreateRecord
{
    protected static string $resource = TareaResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['aso_id'] = session('aso_actual');
        if (empty($data['codigo']) && is_numeric($data['aso_id'])) {
            $data['codigo'] = (Tarea::withTrashed()->where('aso_id', $data['aso_id'])->max('codigo') ?? 0) + 1;
        }
        return $data;
    }
}
