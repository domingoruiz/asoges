<?php

namespace App\Filament\Resources\AsoResource\Pages;

use App\Filament\Resources\AsoResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\Action;
use App\Filament\Actions\AuditAction;
use App\Filament\Actions\FormActions;

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
