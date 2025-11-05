<?php

namespace App\Exports;

use App\Models\LibroContabilidad;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LibroContabilidadExport implements FromCollection, WithHeadings
{
    public function __construct(private Collection $ids) {}

    public function collection()
    {
        return LibroContabilidad::query()
            ->with([
                'aso:id,nombre',
                'tipoTransaccion:id,nombre',
                'ejercicio:id,nombre',
                'moneda:id,nombre',
                'entidad:id,nombre_fiscal',
                'cuentaBancaria:id,iban',
                'categoria:id,nombre',
                'createdBy:id,name',
                'updatedBy:id,name',
            ])
            ->whereIn('id', $this->ids)
            ->get()
            ->map(fn ($r) => [
                'ID'               => $r->id,
                'Fecha contable'   => optional($r->fecha_contable)->format('Y-m-d'),
                'Asociación'       => $r->aso?->nombre,
                'Tipo transacción' => $r->tipoTransaccion?->nombre,
                'Ejercicio'        => $r->ejercicio?->nombre,
                'Moneda'           => $r->moneda?->nombre,
                'Entidad'          => $r->entidad?->nombre_fiscal,
                'Cuenta bancaria'  => $r->cuentaBancaria?->iban,
                'Categoría'        => $r->categoria?->nombre,
                'Importe'          => $r->importe,
                'Concepto'         => $r->concepto,
                'Descripción'      => $r->descripcion,
                'Creado por'       => $r->createdBy?->name,
                'Modificado por'   => $r->updatedBy?->name,
                'Fecha creación'   => optional($r->created_at)->format('Y-m-d H:i:s'),
                'Última modificación' => optional($r->updated_at)->format('Y-m-d H:i:s'),
                'Eliminado en'     => optional($r->deleted_at)->format('Y-m-d H:i:s'),
            ]);
    }

    public function headings(): array
    {
        return [
            'ID','Fecha contable','Asociación','Tipo transacción','Ejercicio','Moneda','Entidad',
            'Cuenta bancaria','Categoría','Importe','Concepto','Descripción',
            'Creado por','Modificado por','Fecha creación','Última modificación','Eliminado en'
        ];
    }
}