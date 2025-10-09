<?php

namespace App\Exports;

use App\Models\CuentaBancaria;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CuentasBancariasExport implements FromCollection, WithHeadings
{
    public function __construct(private Collection $ids) {}

    public function collection()
    {
        return CuentaBancaria::query()
            ->with([
                'entidadRel:id,nombre_fiscal',
                'paisRel:id,nombre',
                'monedaRel:id,codigo_iso,nombre',
            ])
            ->whereIn('id', $this->ids)
            ->get([
                'id',
                'aso_id',
                'nombre',
                'entidad_id',
                'numero_cuenta',
                'swift_bic',
                'moneda_id',
                'fecha_apertura',
                'observaciones',
                'direccion',
                'cp',
                'localidad',
                'provincia',
                'pais_id',
                'telefono',
                'email',
                'created_at',
                'updated_at',
                'deleted_at',
            ])
            ->map(function ($e) {
                return [
                    'id'                => $e->id,
                    'aso_id'            => $e->aso_id,
                    'nombre'            => $e->nombre,
                    'entidad'           => $e->entidadRel?->nombre,
                    'numero_cuenta'    => $e->numero_cuenta,
                    'swift_bic'         => $e->swift_bic,
                    'moneda'            => $e->monedaRel?->codigo_iso,
                    'fecha_apertura'    => $e->fecha_apertura,
                    'observaciones'     => $e->observaciones,
                    'direccion'         => $e->direccion,
                    'cp'                => $e->cp,
                    'localidad'         => $e->localidad,
                    'provincia'         => $e->provincia,
                    'pais'              => $e->paisRel?->nombre,
                    'telefono'          => $e->telefono,
                    'email'             => $e->email,
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
            'Nombre',
            'Entidad',
            'Número de Cuenta',
            'SWIFT/BIC',
            'Moneda',
            'Fecha de Apertura',
            'Observaciones',
            'Dirección',
            'CP',
            'Localidad',
            'Provincia',
            'País',
            'Teléfono',
            'Email',
            'Fecha de Creación',
            'Última Modificación',
            'Eliminado en',
        ];
    }
}
