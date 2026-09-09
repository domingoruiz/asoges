<?php

namespace App\Exports;

use App\Models\LibroInteraccion;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LibroInteraccionesExport implements FromCollection, WithHeadings
{
    public function __construct(private Collection $ids) {}

    public function collection(): Collection
    {
        return LibroInteraccion::query()
            ->with([
                'proyecto:id,nombre',
                'socio:id,nombre,apellidos',
                'contacto:id,nombre_completo',
            ])
            ->whereIn('id', $this->ids)
            ->get([
                'id', 'aso_id', 'codigo', 'fecha', 'libro_proyecto_id',
                'socio_id', 'interaccion', 'oportunidad', 'contacto_id', 'notas',
            ])
            ->map(function ($item) {
                return [
                    'codigo'      => $item->codigo ?? $item->id,
                    'fecha'       => optional($item->fecha)->format('d/m/Y'),
                    'proyecto'    => $item->proyecto?->nombre,
                    'responsable' => $item->socio?->nombre_completo,
                    'interaccion' => $item->interaccion,
                    'oportunidad' => $item->oportunidad,
                    'contacto'    => $item->contacto?->nombre_completo,
                    'notas'       => $item->notas,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Código',
            'Fecha',
            'Proyecto',
            'Responsable',
            'Interacción',
            'Oportunidad',
            'Contacto',
            'Notas',
        ];
    }
}
