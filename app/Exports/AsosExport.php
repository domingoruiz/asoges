<?php

namespace App\Exports;

use App\Models\Aso;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AsosExport implements FromCollection, WithHeadings
{
    public function __construct(private Collection $ids) {}

    public function collection()
    {
        return Aso::query()
            ->with(['createdBy:id,name', 'updatedBy:id,name']) // si tienes relaciones
            ->whereIn('id', $this->ids)
            ->get([
                'id',
                'fch_constitucion',
                'nombre',
                'cif',
                'domicilio_social',
                'nro_registro',
                'nro_registro_municipal',
                'telefono',
                'email',
                'web',
                'alt_usr',
                'mod_usr',
                'created_at',
                'updated_at',
            ])
            ->map(function ($aso) {
                return [
                    'id'                    => $aso->id,
                    'fch_constitucion'      => $aso->fch_constitucion,
                    'nombre'                => $aso->nombre,
                    'cif'                   => $aso->cif,
                    'domicilio_social'      => $aso->domicilio_social,
                    'nro_registro'          => $aso->nro_registro,
                    'nro_registro_municipal'=> $aso->nro_registro_municipal,
                    'telefono'              => $aso->telefono,
                    'email'                 => $aso->email,
                    'web'                   => $aso->web,
                    'creado_por_id'         => $aso->alt_usr,
                    'creado_por'            => $aso->createdBy?->name,
                    'modificado_por_id'     => $aso->mod_usr,
                    'modificado_por'        => $aso->updatedBy?->name,
                    'created_at'            => $aso->created_at,
                    'updated_at'            => $aso->updated_at,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Fecha constitución',
            'Nombre',
            'CIF',
            'Domicilio social',
            'Nº registro',
            'Nº registro municipal',
            'Teléfono',
            'Email',
            'Web',
            'Creado por (ID)',
            'Creado por (Nombre)',
            'Modificado por (ID)',
            'Modificado por (Nombre)',
            'Fecha de creación',
            'Última modificación',
        ];
    }
}