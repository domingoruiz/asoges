<?php

namespace App\Resources\Pais\Pages;

use Filament\Actions\DeleteAction;
use App\Actions\AuditAction;
use App\Actions\FormActions;
use App\Resources\Pais\PaiResource;
use Filament\Resources\Pages\EditRecord;

class EditPai extends EditRecord
{
    protected static string $resource = PaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
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
