<?php

namespace App\Resources\Currencies\Pages;

use Filament\Actions\CreateAction;
use App\Resources\Currencies\CurrencyResource;
use Filament\Resources\Pages\ListRecords;

class ListCurrencies extends ListRecords
{
    protected static string $resource = CurrencyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
