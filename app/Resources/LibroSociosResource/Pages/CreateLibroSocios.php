<?php

namespace App\Resources\LibroSociosResource\Pages;

use App\Resources\LibroSociosResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLibroSocios extends CreateRecord
{
    protected static string $resource = LibroSociosResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['aso_id'] = $data['aso_id'] ?? session('aso_actual');
        return $data;
    }
}