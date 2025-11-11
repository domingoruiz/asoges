<?php

namespace App\Exports;

use App\Models\LibroProyecto;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LibroProyectosExport implements FromCollection, WithHeadings
{
    public function __construct(private Collection $ids) {}

    public function collection(): Collection
    {
        return LibroProyecto::query()
            ->with(['aso:id,nombre'])
            ->whereIn('id', $this->ids)
            ->get([
                'id', 'aso_id', 'nombre', 'estado', 'fecha_inicio', 'fecha_fin', 'observaciones',
            ])
            ->map(function ($p) {
                return [
                    'id'            => $p->id,
                    'asociacion'    => $p->aso?->nombre,
                    'nombre'        => $p->nombre,
                    'estado'        => $p->estado,
                    'fecha_inicio'  => optional($p->fecha_inicio)->format('Y-m-d'),
                    'fecha_fin'     => optional($p->fecha_fin)->format('Y-m-d'),
                    'observaciones' => $p->observaciones,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'ID','Asociación','Nombre','Estado',
            'Fecha inicio','Fecha fin','Observaciones',
        ];
    }
}