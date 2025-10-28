<?php

namespace App\Exports;

use App\Models\LibroSocios;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LibroSociosExport implements FromCollection, WithHeadings
{
    public function __construct(private Collection $ids) {}

    public function collection()
    {
        return LibroSocios::query()
            ->with([
                'createdBy:id,name', 'updatedBy:id,name',
                'aso:id,nombre',
                'rol:id,nombre',
                'tipoSocio:id,nombre',
                'paisRel:id,nombre',
                'continenteRel:id,nombre',
            ])
            ->whereIn('id', $this->ids)
            ->get([
                'id',
                'aso_id',
                'numero_socio',
                'rol_id',
                'tipo_socio_id',
                'pais_id',
                'continente_id',
                'nombre',
                'apellidos',
                'dni',
                'telefono',
                'email',
                'fecha_nacimiento',
                'direccion',
                'cp',
                'localidad',
                'nombre_tutor',
                'dni_tutor',
                'telefono_tutor',
                'alt_usr',
                'mod_usr',
                'created_at',
                'updated_at',
                'deleted_at',
            ])
            ->map(fn ($s) => [
                'id'                 => $s->id,
                'aso_id'             => $s->aso_id,
                'asociacion'         => $s->aso?->nombre,
                'numero_socio'       => $s->numero_socio, // <-- nuevo
                'rol_id'             => $s->rol_id,
                'rol'                => $s->rol?->nombre,
                'tipo_socio_id'      => $s->tipo_socio_id,
                'tipo_socio'         => $s->tipoSocio?->nombre,
                'pais_id'            => $s->pais_id,
                'pais'               => $s->paisRel?->nombre,
                'continente_id'      => $s->continente_id,
                'continente'         => $s->continenteRel?->nombre,
                'nombre'             => $s->nombre,
                'apellidos'          => $s->apellidos,
                'dni'                => $s->dni,
                'telefono'           => $s->telefono,
                'email'              => $s->email,
                'fecha_nacimiento'   => $s->fecha_nacimiento,
                'direccion'          => $s->direccion,
                'cp'                 => $s->cp,
                'localidad'          => $s->localidad,
                'nombre_tutor'       => $s->nombre_tutor,
                'dni_tutor'          => $s->dni_tutor,
                'telefono_tutor'     => $s->telefono_tutor,
                'creado_por'         => $s->createdBy?->name,
                'modificado_por'     => $s->updatedBy?->name,
                'created_at'         => $s->created_at,
                'updated_at'         => $s->updated_at,
                'deleted_at'         => $s->deleted_at,
            ]);
    }

    public function headings(): array
    {
        return [
            'ID',
            'Asociación (ID)',
            'Asociación',
            'Nº de socio',
            'Rol (ID)',
            'Rol',
            'Tipo de socio (ID)',
            'Tipo de socio',
            'País (ID)',
            'País',
            'Continente (ID)',
            'Continente',
            'Nombre',
            'Apellidos',
            'DNI',
            'Teléfono',
            'Email',
            'Fecha nacimiento',
            'Dirección',
            'CP',
            'Localidad',
            'Nombre tutor',
            'DNI tutor',
            'Teléfono tutor',
            'Creado por',
            'Modificado por',
            'Fecha creación',
            'Última modificación',
            'Eliminado en',
        ];
    }
}