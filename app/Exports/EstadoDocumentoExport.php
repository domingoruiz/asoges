<?php

namespace App\Exports;

use App\Models\EstadoDocumento;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EstadoDocumentoExport implements FromCollection, WithHeadings
{
    public function __construct(private Collection $ids) {}

    public function collection(): Collection
    {
        return EstadoDocumento::query()
            ->with(['aso:id,nombre'])
            ->whereIn('id', $this->ids)
            ->get([
                'id', 'aso_id', 'nombre',
            ])
            ->map(function ($r) {
                return [
                    'id'         => $r->id,
                    'asociacion' => $r->aso?->nombre,
                    'nombre'     => $r->nombre,
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