<?php

namespace App\Resources\LibroActasResource\Pages;

use App\Actions\AuditAction;
use App\Actions\FormActions;
use App\Resources\LibroActasResource;
use Filament\Resources\Pages\EditRecord;

class EditLibroActa extends EditRecord
{
    protected static string $resource = LibroActasResource::class;

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