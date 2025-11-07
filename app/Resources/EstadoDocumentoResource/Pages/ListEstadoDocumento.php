<?php

namespace App\Resources\EstadoDocumentoResource\Pages;

use App\Resources\EstadoDocumentoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEstadoDocumento extends ListRecords
{
    protected static string $resource = EstadoDocumentoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}