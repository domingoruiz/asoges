<?php

namespace App\Resources\TipoActaResource\Pages;

use App\Resources\TipoActaResource;
use Filament\Resources\Pages\EditRecord;

class EditTipoActa extends EditRecord
{
    protected static string $resource = TipoActaResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['aso_id'] = $this->record->aso_id ?? session('aso_actual');
        return $data;
    }
}