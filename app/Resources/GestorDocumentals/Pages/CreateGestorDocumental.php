<?php

namespace App\Resources\GestorDocumentals\Pages;

use App\Resources\GestorDocumentals\GestorDocumentalResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;

class CreateGestorDocumental extends CreateRecord
{
    protected static string $resource = GestorDocumentalResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['aso_id'] = session('aso_actual');
        return $data;
    }

    protected function afterCreate(): void
    {
        $path = $this->record->archivo;
        if ($path && str_contains($path, '/pending/')) {
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

}