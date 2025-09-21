<?php

namespace App\Exports;

use App\Models\SocioTipo;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SocioTiposExport implements FromCollection, WithHeadings
{
    public function __construct(private Collection $ids) {}

    public function collection()
    {
        return SocioTipo::query()
            ->with(['createdBy:id,name', 'updatedBy:id,name'])
            ->whereIn('id', $this->ids)
            ->get([
                'id', 'nombre', 'alt_usr', 'mod_usr', 'created_at', 'updated_at', 'deleted_at',
            ])
            ->map(fn ($r) => [
                'id'         => $r->id,
                'nombre'     => $r->nombre,
                'alt_usr'    => $r->alt_usr,
                'alt_usr_nombre' => $r->createdBy?->name,
                'mod_usr'    => $r->mod_usr,
                'mod_usr_nombre' => $r->updatedBy?->name,
                'created_at' => $r->created_at,
                'updated_at' => $r->updated_at,
                'deleted_at' => $r->deleted_at,
            ]);
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nombre',
            'Creado por (ID)',
            'Creado por (Nombre)',
            'Modificado por (ID)',
            'Modificado por (Nombre)',
            'Fecha creación',
            'Última modificación',
            'Borrado (papelera)',
        ];
    }
}
