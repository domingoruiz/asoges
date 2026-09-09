<?php

namespace App\Resources\LibroInteraccions\Pages;

use App\Models\LibroInteraccion;
use App\Resources\LibroInteraccions\LibroInteraccionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLibroInteraccion extends CreateRecord
{
    protected static string $resource = LibroInteraccionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['aso_id'] = session('aso_actual');
        if (empty($data['codigo']) && is_numeric($data['aso_id'])) {
            $data['codigo'] = (LibroInteraccion::withTrashed()->where('aso_id', $data['aso_id'])->max('codigo') ?? 0) + 1;
        }
        return $data;
    }
}
