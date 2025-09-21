<?php

namespace App\Resources\ContinentResource\Pages;

use App\Actions\AuditAction;
use App\Actions\FormActions;
use App\Resources\ContinentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditContinent extends EditRecord
{
    protected static string $resource = ContinentResource::class;

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
