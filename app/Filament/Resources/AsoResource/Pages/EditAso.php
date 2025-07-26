<?php

namespace App\Filament\Resources\AsoResource\Pages;

use App\Filament\Resources\AsoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAso extends EditRecord
{
    protected static string $resource = AsoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
