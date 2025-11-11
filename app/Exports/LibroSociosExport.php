<?php

namespace App\Exports;

use App\Models\LibroSocios;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LibroSociosExport implements FromCollection, WithHeadings
{
    public function __construct(private Collection $ids) {}

    public function collection(): Collection
    {
        return LibroSocios::query()
            ->with([
                'aso:id,nombre',
                'rol:id,nombre',
                'tipoSocio:id,nombre',
                'paisRel:id,nombre',
                'continenteRel:id,nombre',
            ])
            ->whereIn('id', $this->ids)
            ->get([
                'id', 'aso_id', 'numero_socio', 'rol_id', 'tipo_socio_id', 'pais_id', 'continente_id',
                'nombre', 'apellidos', 'dni', 'telefono', 'email', 'fecha_nacimiento', 'direccion',
                'cp', 'localidad', 'nombre_tutor', 'dni_tutor', 'telefono_tutor',
            ])
            ->map(function ($s) {
                return [
                    'id'               => $s->id,
                    'asociacion'       => $s->aso?->nombre,
                    'numero_socio'     => $s->numero_socio,
                    'rol'              => $s->rol?->nombre,
                    'tipo_socio'       => $s->tipoSocio?->nombre,
                    'pais'             => $s->paisRel?->nombre,
                    'continente'       => $s->continenteRel?->nombre,
                    'nombre'           => $s->nombre,
                    'apellidos'        => $s->apellidos,
                    'dni'              => $s->dni,
                    'telefono'         => $s->telefono,
                    'email'            => $s->email,
                    'fecha_nacimiento' => optional($s->fecha_nacimiento)->format('Y-m-d'),
                    'direccion'        => $s->direccion,
                    'cp'               => $s->cp,
                    'localidad'        => $s->localidad,
                    'nombre_tutor'     => $s->nombre_tutor,
                    'dni_tutor'        => $s->dni_tutor,
                    'telefono_tutor'   => $s->telefono_tutor,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'ID','Asociación','Nº de socio','Rol','Tipo de socio','País','Continente',
            'Nombre','Apellidos','DNI','Teléfono','Email','Fecha nacimiento',
            'Dirección','CP','Localidad','Nombre tutor','DNI tutor','Teléfono tutor',
        ];
    }
}