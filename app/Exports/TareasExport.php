<?php

namespace App\Exports;

use App\Models\Tarea;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TareasExport implements FromCollection, WithHeadings
{
    public function __construct(private Collection $ids) {}

    public function collection(): Collection
    {
        return Tarea::query()
            ->with(['responsable:id,nombre,apellidos'])
            ->whereIn('id', $this->ids)
            ->get([
                'id', 'aso_id', 'codigo', 'fecha', 'nombre',
                'socio_id', 'frecuencia', 'estado', 'descripcion',
            ])
            ->map(function ($item) {
                return [
                    'codigo'       => $item->codigo ?? $item->id,
                    'fecha'        => optional($item->fecha)->format('d/m/Y'),
                    'nombre'       => $item->nombre,
                    'responsable'  => $item->responsable?->nombre_completo,
                    'periodicidad' => ucfirst($item->frecuencia),
                    'estado'       => ucfirst(str_replace('_', ' ', $item->estado)),
                    'descripcion'  => $item->descripcion,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Código',
            'Fecha',
            'Nombre',
            'Responsable',
            'Periodicidad',
            'Estado',
            'Descripción',
        ];
    }
}
