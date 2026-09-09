<?php

namespace App\Resources\Contactos\Pages;

use App\Actions\AuditAction;
use App\Actions\FormActions;
use App\Resources\Contactos\ContactoResource;
use Filament\Resources\Pages\EditRecord;

class EditContacto extends EditRecord
{
    protected static string $resource = ContactoResource::class;

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
