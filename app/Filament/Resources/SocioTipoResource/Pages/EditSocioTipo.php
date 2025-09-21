<?php

namespace App\Filament\Resources\SocioTipoResource\Pages;

use App\Filament\Actions\FormActions;
use App\Filament\Resources\SocioTipoResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions;
use App\Filament\Actions\AuditAction;

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
