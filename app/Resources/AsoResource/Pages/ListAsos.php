<?php

namespace App\Resources\AsoResource\Pages;

use App\Resources\AsoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAsos extends ListRecords
{
    protected static string $resource = AsoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
