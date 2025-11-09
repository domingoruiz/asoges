<?php

namespace App\Resources\EstadoDocumentos\Pages;

use Filament\Actions\CreateAction;
use App\Resources\EstadoDocumentos\EstadoDocumentoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEstadoDocumento extends ListRecords
{
    protected static string $resource = EstadoDocumentoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}