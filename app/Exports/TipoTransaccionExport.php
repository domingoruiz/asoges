<?php

namespace App\Exports;

use App\Models\TipoTransaccion;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TipoTransaccionExport implements FromCollection, WithHeadings
{
    public function __construct(private Collection $ids) {}

    public function collection()
    {
        return TipoTransaccion::query()
            ->with(['aso:id,nombre','createdBy:id,name','updatedBy:id,name'])
            ->whereIn('id', $this->ids)
            ->get([
                'id',
                'aso_id',
                'nombre',
                'alt_usr',
                'mod_usr',
                'created_at',
                'updated_at',
                'deleted_at',
            ])
            ->map(fn ($r) => [
                'id'             => $r->id,
                'aso_id'         => $r->aso_id,
                'asociacion'     => $r->aso?->nombre,
                'nombre'         => $r->nombre,
                'creado_por'     => $r->createdBy?->name,
                'modificado_por' => $r->updatedBy?->name,
                'created_at'     => optional($r->created_at)->format('Y-m-d H:i:s'),
                'updated_at'     => optional($r->updated_at)->format('Y-m-d H:i:s'),
                'deleted_at'     => optional($r->deleted_at)->format('Y-m-d H:i:s'),
            ]);
    }

    public function headings(): array
    {
        return [
            'ID',
            'Asociación (ID)',
            'Asociación',
            'Nombre',
            'Creado por',
            'Modificado por',
            'Fecha creación',
            'Última modificación',
            'Eliminado en',
        ];
    }
}