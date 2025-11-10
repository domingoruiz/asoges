<?php

namespace App\Exports;

use App\Models\LibroProyecto;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LibroProyectosExport implements FromCollection, WithHeadings
{
    public function __construct(private Collection $ids) {}

    public function collection()
    {
        return LibroProyecto::query()
            ->with(['createdBy:id,name', 'updatedBy:id,name'])
            ->whereIn('id', $this->ids)
            ->get([
                'id',
                'aso_id',
                'nombre',
                'estado',
                'fecha_inicio',
                'fecha_fin',
                'observaciones',
                'alt_usr',
                'mod_usr',
                'created_at',
                'updated_at',
                'deleted_at',
            ])
            ->map(function ($p) {
                return [
                    'id'                => $p->id,
                    'aso_id'            => $p->aso_id,
                    'nombre'            => $p->nombre,
                    'estado'            => $p->estado,
                    'fecha_inicio'      => $p->fecha_inicio,
                    'fecha_fin'         => $p->fecha_fin,
                    'observaciones'     => $p->observaciones,
                    'creado_por_id'     => $p->alt_usr,
                    'creado_por'        => $p->createdBy?->name,
                    'modificado_por_id' => $p->mod_usr,
                    'modificado_por'    => $p->updatedBy?->name,
                    'created_at'        => $p->created_at,
                    'updated_at'        => $p->updated_at,
                    'deleted_at'        => $p->deleted_at,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Asociación (ID)',
            'Nombre',
            'Estado',
            'Fecha inicio',
            'Fecha fin',
            'Observaciones',
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