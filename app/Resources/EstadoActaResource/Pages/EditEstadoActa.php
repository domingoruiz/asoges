<?php

namespace App\Resources\EstadoActaResource\Pages;

use App\Actions\AuditAction;
use App\Actions\FormActions;
use App\Resources\EstadoActaResource;
use Filament\Resources\Pages\EditRecord;

class EditEstadoActa extends EditRecord
{
    protected static string $resource = EstadoActaResource::class;

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
            AuditAction::make()
        ];
    }
}