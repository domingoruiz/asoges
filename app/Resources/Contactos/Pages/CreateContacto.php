<?php

namespace App\Resources\Contactos\Pages;

use App\Resources\Contactos\ContactoResource;
use Filament\Resources\Pages\CreateRecord;

class CreateContacto extends CreateRecord
{
    protected static string $resource = ContactoResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['aso_id'] = session('aso_actual');
        if (empty($data['codigo']) && is_numeric($data['aso_id'])) {
            $data['codigo'] = (\App\Models\Contacto::withTrashed()->where('aso_id', $data['aso_id'])->max('codigo') ?? 0) + 1;
        }
        return $data;
    }
}
