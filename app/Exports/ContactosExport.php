<?php

namespace App\Exports;

use App\Models\Contacto;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ContactosExport implements FromCollection, WithHeadings
{
    public function __construct(private Collection $ids) {}

    public function collection(): Collection
    {
        return Contacto::query()
            ->with(['socio:id,nombre,apellidos', 'entidad:id,nombre_fiscal'])
            ->whereIn('id', $this->ids)
            ->get([
                'id', 'aso_id', 'codigo', 'nombre_completo', 'tipo',
                'socio_id', 'entidad_id', 'posicion', 'email', 'telefono', 'notas',
            ])
            ->map(function ($c) {
                return [
                    'codigo'          => $c->codigo ?? $c->id,
                    'nombre_completo' => $c->nombre_completo,
                    'tipo'            => $c->tipo,
                    'responsable'     => $c->socio?->nombre_completo,
                    'organizacion'    => $c->entidad?->nombre_fiscal,
                    'posicion'        => $c->posicion,
                    'email'           => $c->email,
                    'telefono'        => $c->telefono,
                    'notas'           => $c->notas,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Código',
            'Nombre completo',
            'Tipo',
            'Responsable',
            'Organización',
            'Posición',
            'Email',
            'Teléfono',
            'Notas',
        ];
    }
}
