<?php

namespace App\Exports;

use App\Models\LibroContabilidad;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LibroContabilidadExport implements FromCollection, WithHeadings
{
    public function __construct(private Collection $ids) {}

    public function collection(): Collection
    {
        return LibroContabilidad::query()
            ->with([
                'aso:id,nombre',
                'tipoTransaccion:id,nombre',
                'ejercicio:id,nombre',
                'moneda:id,nombre',
                'entidad:id,nombre_fiscal',
                'cuentaBancaria:id,numero_cuenta',
                'categoria:id,nombre',
            ])
            ->whereIn('id', $this->ids)
            ->get([
                'id', 'aso_id', 'fecha_contable', 'tipo_transaccion_id', 'ejercicio_id', 'moneda_id',
                'entidad_id', 'cuenta_bancaria_id', 'categoria_id', 'importe', 'concepto', 'descripcion',
            ])
            ->map(function ($r) {
                return [
                    'id'               => $r->id,
                    'asociacion'       => $r->aso?->nombre,
                    'fecha_contable'   => optional($r->fecha_contable)->format('Y-m-d'),
                    'tipo_transaccion' => $r->tipoTransaccion?->nombre,
                    'ejercicio'        => $r->ejercicio?->nombre,
                    'moneda'           => $r->moneda?->nombre,
                    'entidad'          => $r->entidad?->nombre_fiscal,
                    'cuenta_bancaria'  => $r->cuentaBancaria?->numero_cuenta,
                    'categoria'        => $r->categoria?->nombre,
                    'importe'          => $r->importe,
                    'concepto'         => $r->concepto,
                    'descripcion'      => $r->descripcion,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'ID','Asociación','Fecha contable','Tipo transacción','Ejercicio','Moneda',
            'Entidad','Cuenta bancaria','Categoría','Importe','Concepto','Descripción',
        ];
    }
}