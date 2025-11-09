<?php

namespace App\Resources\GestorDocumentals\Pages;

use App\Actions\AuditAction;
use App\Actions\FormActions;
use App\Resources\GestorDocumentals\GestorDocumentalResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditGestorDocumental extends EditRecord
{
    protected static string $resource = GestorDocumentalResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['aso_id'] = $this->record->aso_id ?? session('aso_actual');
        return $data;
    }

    protected function afterSave(): void
    {
        $path = $this->record->archivo;
        if ($path && !str_contains($path, '/' . $this->record->id . '/')) {
            $filename = basename($path);
            $targetDir = 'gestor_documental/' . $this->record->aso_id . '/' . $this->record->id;
            Storage::disk('public')->makeDirectory($targetDir);
            $newPath = $targetDir . '/' . $filename;
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->move($path, $newPath);
                $this->record->update(['archivo' => $newPath]);
            }
        }
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