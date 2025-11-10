<?php

namespace App\Resources\LibroProyectos\Pages;

use Filament\Resources\Pages\EditRecord;
use App\Resources\LibroProyectos\LibroProyectoResource;
use App\Actions\AuditAction;
use App\Actions\FormActions;

class EditLibroProyecto extends EditRecord
{
    protected static string $resource = LibroProyectoResource::class;

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