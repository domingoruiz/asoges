<?php

namespace App\Exports;

use App\Models\Ejercicio;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EjerciciosExport implements FromCollection, WithHeadings
{
    public function __construct(private Collection $ids) {}

    public function collection()
    {
        return Ejercicio::query()
            ->with(['createdBy:id,name', 'updatedBy:id,name'])
            ->whereIn('id', $this->ids)
            ->get([
                'id',
                'aso_id',
                'nombre',
                'fch_inicio',
                'fch_fin',
                'alt_usr',
                'mod_usr',
                'created_at',
                'updated_at',
                'deleted_at',
            ])
            ->map(function ($e) {
                return [
                    'id'                => $e->id,
                    'aso_id'            => $e->aso_id,
                    'nombre'            => $e->nombre,
                    'fch_inicio'        => $e->fch_inicio?->format('Y-m-d'),
                    'fch_fin'           => $e->fch_fin?->format('Y-m-d'),
                    'creado_por_id'     => $e->alt_usr,
                    'creado_por'        => $e->createdBy?->name,
                    'modificado_por_id' => $e->mod_usr,
                    'modificado_por'    => $e->updatedBy?->name,
                    'created_at'        => $e->created_at,
                    'updated_at'        => $e->updated_at,
                    'deleted_at'        => $e->deleted_at,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Asociación (ID)',
            'Nombre',
            'Fecha inicio',
            'Fecha fin',
            'Creado por (ID)',
            'Creado por (Nombre)',
            'Modificado por (ID)',
            'Modificado por (Nombre)',
            'Fecha de creación',
            'Última modificación',
            'Eliminado en',
        ];
    }
}