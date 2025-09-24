<?php

namespace App\Filament\Resources\ContinentResource\Pages;

use App\Filament\Resources\ContinentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Actions\AuditAction;
use App\Filament\Actions\FormActions;

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
