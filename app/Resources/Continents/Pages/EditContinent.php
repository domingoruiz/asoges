<?php

namespace App\Resources\Continents\Pages;

use Filament\Actions\DeleteAction;
use App\Actions\AuditAction;
use App\Actions\FormActions;
use App\Resources\Continents\ContinentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditContinent extends EditRecord
{
    protected static string $resource = ContinentResource::class;

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
