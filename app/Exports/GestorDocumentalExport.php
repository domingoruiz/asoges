<?php

namespace App\Exports;

use App\Models\GestorDocumental;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class GestorDocumentalExport implements FromCollection, WithHeadings
{
    public function __construct(private Collection $ids) {}

    public function collection(): Collection
    {
        return GestorDocumental::query()
            ->with(['aso:id,nombre', 'tipoDocumento:id,nombre,categoria_padre_id', 'entidad:id,nombre_fiscal', 'ejercicio:id,nombre', 'estado:id,nombre'])
            ->whereIn('id', $this->ids)
            ->get([
                'id', 'aso_id', 'tipo_documento_id', 'entidad_id', 'ejercicio_id', 'estado_documento',
                'direccion_documento', 'fecha_documento', 'numero_serie', 'ref_externa', 'nombre',
                'descripcion', 'archivo',
            ])
            ->map(function ($r) {
                $rutaTipo = $r->tipoDocumento?->ruta ?? $r->tipoDocumento?->nombre;
                return [
                    'id'              => $r->id,
                    'asociacion'      => $r->aso?->nombre,
                    'tipo_documento'  => $rutaTipo,
                    'entidad'         => $r->entidad?->nombre_fiscal,
                    'ejercicio'       => $r->ejercicio?->nombre,
                    'estado'          => $r->estado?->nombre,
                    'direccion'       => $r->direccion_documento,
                    'fecha_documento' => optional($r->fecha_documento)->format('Y-m-d'),
                    'numero_serie'    => $r->numero_serie,
                    'ref_externa'     => $r->ref_externa,
                    'nombre'          => $r->nombre,
                    'descripcion'     => $r->descripcion,
                    'archivo'         => $r->archivo,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'ID','Asociación','Tipo de documento','Entidad','Ejercicio','Estado',
            'Dirección','Fecha documento','Nº serie','Ref. externa','Nombre',
            'Descripción','Archivo',
        ];
    }
}