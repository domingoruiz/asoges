<?php

namespace App\Resources\LibroActas\Pages;

use App\Resources\LibroActas\LibroActasResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLibroActa extends CreateRecord
{
    protected static string $resource = LibroActasResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['aso_id'] = $data['aso_id'] ?? session('aso_actual');
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }
}