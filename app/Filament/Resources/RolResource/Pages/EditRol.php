<?php

namespace App\Filament\Resources\RolResource\Pages;

use App\Filament\Resources\RolResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\Action;
use App\Filament\Actions\AuditAction;
use App\Filament\Actions\FormActions;

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
