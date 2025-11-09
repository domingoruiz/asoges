<?php

namespace App\Resources\SocioTipos\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreAction;
use App\Actions\AuditAction;
use App\Actions\FormActions;
use App\Resources\SocioTipos\SocioTipoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSocioTipo extends EditRecord
{
    protected static string $resource = SocioTipoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            RestoreAction::make()
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
