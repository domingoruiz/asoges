<?php

namespace App\Exports;

use App\Models\TipoDocumento;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TipoDocumentoExport implements FromCollection, WithHeadings
{
    public function __construct(private Collection $ids) {}

    public function collection()
    {
        return TipoDocumento::query()
            ->with(['categoriaPadre:id,nombre','createdBy:id,name','updatedBy:id,name'])
            ->whereIn('id', $this->ids)
            ->get(['id','aso_id','nombre','categoria_padre_id','alt_usr','mod_usr','created_at','updated_at','deleted_at'])
            ->map(fn($r)=>[
                'id' => $r->id,
                'aso_id' => $r->aso_id,
                'nombre' => $r->nombre,
                'categoria_padre' => $r->categoriaPadre?->nombre,
                'creado_por' => $r->createdBy?->name,
                'modificado_por' => $r->updatedBy?->name,
                'created_at' => $r->created_at,
                'updated_at' => $r->updated_at,
                'deleted_at' => $r->deleted_at,
            ]);
    }

    public function headings(): array
    {
        return [
            'ID',
            'Asociación (ID)',
            'Nombre',
            'Categoría padre',
            'Creado por',
            'Modificado por',
            'Fecha creación',
            'Última modificación',
            'Eliminado en',
        ];
    }
}