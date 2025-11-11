<?php

namespace App\Exports;

use App\Models\Ejercicio;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EjerciciosExport implements FromCollection, WithHeadings
{
    public function __construct(private Collection $ids) {}

    public function collection(): Collection
    {
        return Ejercicio::query()
            ->with(['aso:id,nombre'])
            ->whereIn('id', $this->ids)
            ->get([
                'id', 'aso_id', 'nombre', 'fch_inicio', 'fch_fin',
            ])
            ->map(function ($e) {
                return [
                    'id'          => $e->id,
                    'asociacion'  => $e->aso?->nombre,
                    'nombre'      => $e->nombre,
                    'fch_inicio'  => optional($e->fch_inicio)->format('Y-m-d'),
                    'fch_fin'     => optional($e->fch_fin)->format('Y-m-d'),
                ];
            });
    }

    public function headings(): array
    {
        return [
            'ID','Asociación','Nombre','Fecha inicio','Fecha fin',
        ];
    }
}