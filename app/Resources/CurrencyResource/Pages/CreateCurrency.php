<?php

namespace App\Resources\CurrencyResource\Pages;

use App\Resources\CurrencyResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCurrency extends CreateRecord
{
    protected static string $resource = CurrencyResource::class;
}
