<?php

namespace App\Exports;

use App\Models\EstadoDocumento;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EstadoDocumentoExport implements FromCollection, WithHeadings
{
    public function __construct(private Collection $ids) {}

    public function collection()
    {
        return EstadoDocumento::query()
            ->with(['createdBy:id,name','updatedBy:id,name'])
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
            ->map(fn($r)=>[
                'id' => $r->id,
                'aso_id' => $r->aso_id,
                'nombre' => $r->nombre,
                'alt_usr' => $r->alt_usr,
                'creado_por' => $r->createdBy?->name,
                'mod_usr' => $r->mod_usr,
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
            'Creado por (ID)',
            'Creado por',
            'Modificado por (ID)',
            'Modificado por',
            'Fecha creación',
            'Última modificación',
            'Eliminado en',
        ];
    }
}