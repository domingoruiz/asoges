<?php

namespace App\Resources\SocioTipoResource\Pages;

use App\Actions\AuditAction;
use App\Actions\FormActions;
use App\Resources\SocioTipoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSocioTipo extends EditRecord
{
    protected static string $resource = SocioTipoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\RestoreAction::make()
        ];
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
