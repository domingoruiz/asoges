<?php

namespace App\Exports;

use App\Models\CuentaBancaria;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CuentasBancariasExport implements FromCollection, WithHeadings
{
    public function __construct(private Collection $ids) {}

    public function collection(): Collection
    {
        return CuentaBancaria::query()
            ->with(['aso:id,nombre', 'entidadRel:id,nombre_fiscal', 'paisRel:id,nombre', 'monedaRel:id,codigo_iso,nombre'])
            ->whereIn('id', $this->ids)
            ->get([
                'id', 'aso_id', 'nombre', 'entidad_id', 'numero_cuenta', 'swift_bic',
                'moneda_id', 'fecha_apertura', 'observaciones', 'direccion', 'cp',
                'localidad', 'provincia', 'pais_id', 'telefono', 'email',
            ])
            ->map(function ($e) {
                return [
                    'id'             => $e->id,
                    'asociacion'     => $e->aso?->nombre,
                    'nombre'         => $e->nombre,
                    'entidad'        => $e->entidadRel?->nombre_fiscal,
                    'numero_cuenta'  => $e->numero_cuenta,
                    'swift_bic'      => $e->swift_bic,
                    'moneda'         => $e->monedaRel?->codigo_iso,
                    'fecha_apertura' => optional($e->fecha_apertura)->format('Y-m-d'),
                    'observaciones'  => $e->observaciones,
                    'direccion'      => $e->direccion,
                    'cp'             => $e->cp,
                    'localidad'      => $e->localidad,
                    'provincia'      => $e->provincia,
                    'pais'           => $e->paisRel?->nombre,
                    'telefono'       => $e->telefono,
                    'email'          => $e->email,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'ID','Asociación','Nombre','Entidad','Número de cuenta','SWIFT/BIC','Moneda','Fecha de apertura',
            'Observaciones','Dirección','CP','Localidad','Provincia','País','Teléfono','Email',
        ];
    }
}