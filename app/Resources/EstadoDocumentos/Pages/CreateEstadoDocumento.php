<?php

namespace App\Resources\EstadoDocumentos\Pages;

use App\Actions\AuditAction;
use App\Actions\FormActions;
use App\Resources\EstadoDocumentos\EstadoDocumentoResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEstadoDocumento extends CreateRecord
{
    protected static string $resource = EstadoDocumentoResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['aso_id'] = $data['aso_id'] ?? session('aso_actual');
        return $data;
    }

    protected function getFormActions(): array
    {
        return [
            FormActions::accept(),
            FormActions::cancel($this->getResource()::getUrl('index')),
            AuditAction::make(),
        ];
    }
}