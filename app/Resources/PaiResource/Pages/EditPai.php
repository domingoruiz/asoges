<?php

namespace App\Resources\PaiResource\Pages;

use App\Actions\AuditAction;
use App\Actions\FormActions;
use App\Resources\PaiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPai extends EditRecord
{
    protected static string $resource = PaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
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
