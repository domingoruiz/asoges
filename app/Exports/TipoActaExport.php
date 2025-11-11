<?php

namespace App\Exports;

use App\Models\TipoActa;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TipoActaExport implements FromCollection, WithHeadings
{
    public function __construct(private Collection $ids) {}

    public function collection(): Collection
    {
        return TipoActa::query()
            ->with(['aso:id,nombre'])
            ->whereIn('id', $this->ids)
            ->get([
                'id', 'aso_id', 'nombre',
            ])
            ->map(function ($r) {
                return [
                    'id'          => $r->id,
                    'asociacion'  => $r->aso?->nombre,
                    'nombre'      => $r->nombre,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'ID','Asociación','Nombre',
        ];
    }
}