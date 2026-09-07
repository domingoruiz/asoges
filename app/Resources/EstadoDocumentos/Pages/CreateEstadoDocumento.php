<?php

namespace App\Resources\EstadoDocumentos\Pages;

use App\Resources\EstadoDocumentos\EstadoDocumentoResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEstadoDocumento extends CreateRecord
{
    protected static string $resource = EstadoDocumentoResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['aso_id'] = session('aso_actual');
        return $data;
    }

}