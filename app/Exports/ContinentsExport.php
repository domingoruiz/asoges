<?php

namespace App\Exports;

use App\Models\Continent;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ContinentsExport implements FromCollection, WithHeadings
{
    private Collection $ids;

    public function __construct(Collection $ids)
    {
        $this->ids = $ids;
    }

    public function collection()
    {
        return Continent::query()
            ->whereIn('id', $this->ids)
            ->get(['id', 'codigo', 'nombre']);
    }

    public function headings(): array
    {
        return ['ID', 'Código', 'Nombre'];
    }
}
