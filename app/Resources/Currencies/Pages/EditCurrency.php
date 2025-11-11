<?php

namespace App\Resources\Currencies\Pages;

use Filament\Actions\DeleteAction;
use App\Actions\AuditAction;
use App\Actions\FormActions;
use App\Resources\Currencies\CurrencyResource;
use Filament\Resources\Pages\EditRecord;

class EditCurrency extends EditRecord
{
    protected static string $resource = CurrencyResource::class;

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
