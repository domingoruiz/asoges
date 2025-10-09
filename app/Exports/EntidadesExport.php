<?php

namespace App\Exports;

use App\Models\Entidad;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EntidadesExport implements FromCollection, WithHeadings
{
    public function __construct(private Collection $ids) {}

    public function collection()
    {
        return Entidad::query()
            ->with([
                'createdBy:id,name', 'updatedBy:id,name',
                'paisRel:id,nombre',
                'continenteRel:id,nombre',
                'monedaRel:id,codigo_iso,nombre,simbolo',
            ])
            ->whereIn('id', $this->ids)
            ->get([
                'id',
                'aso_id',
                'nombre_fiscal',
                'cif',
                'direccion',
                'cp',
                'localidad',
                'provincia',
                'pais',
                'continente',
                'telefono',
                'email',
                'web',
                'swift_bic',
                'iban',
                'moneda',
                'observaciones',
                'alt_usr',
                'mod_usr',
                'created_at',
                'updated_at',
                'deleted_at',
            ])
            ->map(function ($e) {
                return [
                    'id'                => $e->id,
                    'aso_id'            => $e->aso_id,
                    'nombre_fiscal'     => $e->nombre_fiscal,
                    'cif'               => $e->cif,
                    'direccion'         => $e->direccion,
                    'cp'                => $e->cp,
                    'localidad'         => $e->localidad,
                    'provincia'         => $e->provincia,
                    'pais_id'           => $e->pais,
                    'pais'              => $e->paisRel?->nombre,
                    'continente_id'     => $e->continente,
                    'continente'        => $e->continenteRel?->nombre,
                    'telefono'          => $e->telefono,
                    'email'             => $e->email,
                    'web'               => $e->web,
                    'swift_bic'         => $e->swift_bic,
                    'iban'              => $e->iban,
                    'moneda_id'         => $e->moneda,
                    'moneda'            => $e->monedaRel?->codigo_iso,
                    'observaciones'     => $e->observaciones,
                    'creado_por_id'     => $e->alt_usr,
                    'creado_por'        => $e->createdBy?->name,
                    'modificado_por_id' => $e->mod_usr,
                    'modificado_por'    => $e->updatedBy?->name,
                    'created_at'        => $e->created_at,
                    'updated_at'        => $e->updated_at,
                    'deleted_at'        => $e->deleted_at,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Asociación (ID)',
            'Nombre fiscal',
            'CIF',
            'Dirección',
            'CP',
            'Localidad',
            'Provincia',
            'País (ID)',
            'País',
            'Continente (ID)',
            'Continente',
            'Teléfono',
            'Email',
            'Web',
            'SWIFT/BIC',
            'IBAN',
            'Moneda (ID)',
            'Moneda',
            'Observaciones',
            'Creado por (ID)',
            'Creado por (Nombre)',
            'Modificado por (ID)',
            'Modificado por (Nombre)',
            'Fecha de creación',
            'Última modificación',
            'Eliminado en',
        ];
    }
}