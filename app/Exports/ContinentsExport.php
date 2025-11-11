<?php

namespace App\Exports;

use App\Models\Continent;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ContinentsExport implements FromCollection, WithHeadings
{
    public function __construct(private Collection $ids) {}

    public function collection(): Collection
    {
        return Continent::query()
            ->whereIn('id', $this->ids)
            ->get([
                'id', 'codigo', 'nombre',
            ])
            ->map(function ($r) {
                return [
                    'id'      => $r->id,
                    'codigo'  => $r->codigo,
                    'nombre'  => $r->nombre,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'ID','Código','Nombre',
        ];
    }
}