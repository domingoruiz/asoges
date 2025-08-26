<?php

namespace App\Exports;

use App\Models\Rol;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RolesExport implements FromCollection, WithHeadings
{
    public function __construct(private Collection $ids) {}

    public function collection()
    {
        return Rol::query()
            ->whereIn('id', $this->ids)
            ->get([
                'id',
                'nombre',
                'alt_usr',
                'mod_usr',
                'created_at',
                'updated_at',
            ])
            ->map(function ($rol) {
                return [
                    'id'         => $rol->id,
                    'nombre'     => $rol->nombre,
                    'alt_usr'    => $rol->alt_usr,
                    'mod_usr'    => $rol->mod_usr,
                    'created_at' => $rol->created_at,
                    'updated_at' => $rol->updated_at,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nombre',
            'Creado por (alt_usr)',
            'Modificado por (mod_usr)',
            'Fecha de creación',
            'Última modificación',
        ];
    }
}