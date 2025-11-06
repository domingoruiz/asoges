<?php

namespace App\Resources\LibroInventarioResource\Pages;

use App\Actions\AuditAction;
use App\Actions\FormActions;
use App\Resources\LibroInventarioResource;
use Filament\Resources\Pages\EditRecord;

class EditLibroInventario extends EditRecord
{
    protected static string $resource = LibroInventarioResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['aso_id'] = $this->record->aso_id ?? session('aso_actual');
        return $data;
    }

    protected function getFormActions(): array
    {
        return [
            FormActions::accept(),
            FormActions::cancel($this->getResource()::getUrl('index')),
            AuditAction::make(),
        ];
    }
}