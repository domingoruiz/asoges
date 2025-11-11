<?php

namespace App\Resources\SocioTipos\Pages;

use App\Actions\AuditAction;
use App\Actions\FormActions;
use App\Resources\SocioTipos\SocioTipoResource;
use Filament\Resources\Pages\EditRecord;

class EditSocioTipo extends EditRecord
{
    protected static string $resource = SocioTipoResource::class;

    protected function getFormActions(): array
    {
        return [
            FormActions::accept(),
            FormActions::cancel($this->getResource()::getUrl('index')),
            AuditAction::make()
        ];
    }
}
