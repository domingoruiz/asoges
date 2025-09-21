<?php

namespace App\Resources\AsoResource\Pages;

use App\Actions\AuditAction;
use App\Actions\FormActions;
use App\Resources\AsoResource;
use Filament\Resources\Pages\EditRecord;

class EditAso extends EditRecord
{
    protected static string $resource = AsoResource::class;

    protected function getFormActions(): array
    {
        return [
            FormActions::accept(),
            FormActions::cancel($this->getResource()::getUrl('index')),
            AuditAction::make()
        ];
    }
}
