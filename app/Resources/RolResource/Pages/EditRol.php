<?php

namespace App\Resources\RolResource\Pages;

use App\Actions\AuditAction;
use App\Actions\FormActions;
use App\Resources\RolResource;
use Filament\Resources\Pages\EditRecord;

class EditRol extends EditRecord
{
    protected static string $resource = RolResource::class;

    protected function getFormActions(): array
    {
        return [
            FormActions::accept(),
            FormActions::cancel($this->getResource()::getUrl('index')),
            AuditAction::make()
        ];
    }
}
