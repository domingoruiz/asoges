<?php

namespace App\Exports;

use App\Models\Ubicacion;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UbicacionExport implements FromCollection, WithHeadings
{
    public function __construct(private Collection $ids) {}

    public function collection()
    {
        return Ubicacion::query()
            ->with(['aso:id,nombre','padre:id,nombre,categoria_padre_id','createdBy:id,name','updatedBy:id,name'])
            ->whereIn('id', $this->ids)
            ->get()
            ->map(fn ($r) => [
                'ID' => $r->id,
                'Asociación (ID)' => $r->aso_id,
                'Asociación' => $r->aso?->nombre,
                'Nombre' => $r->nombre,
                'Descripción' => $r->descripcion,
                'Ubicación padre' => $r->padre?->nombre,
                'Ruta' => $r->ruta,
                'Creado por' => $r->createdBy?->name,
                'Modificado por' => $r->updatedBy?->name,
                'Fecha creación' => optional($r->created_at)->format('Y-m-d H:i:s'),
                'Última modificación' => optional($r->updated_at)->format('Y-m-d H:i:s'),
                'Eliminado en' => optional($r->deleted_at)->format('Y-m-d H:i:s'),
            ]);
    }

    public function headings(): array
    {
        return [
            'ID','Asociación (ID)','Asociación','Nombre','Descripción','Ubicación padre','Ruta',
            'Creado por','Modificado por','Fecha creación','Última modificación','Eliminado en',
        ];
    }
}