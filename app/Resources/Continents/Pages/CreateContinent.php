<?php

namespace App\Resources\Continents\Pages;

use App\Resources\Continents\ContinentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateContinent extends CreateRecord
{
    protected static string $resource = ContinentResource::class;
}
