<?php

namespace App\Filament\Resources\PaiResource\Pages;

use App\Filament\Resources\PaiResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions;
use App\Filament\Actions\AuditAction;
use App\Filament\Actions\FormActions;

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
