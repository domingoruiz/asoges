<?php

namespace App\Exports;

use App\Models\GestorDocumental;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class GestorDocumentalExport implements FromCollection, WithHeadings
{
    public function __construct(private Collection $ids) {}

    public function collection()
    {
        return GestorDocumental::query()
            ->with([
                'createdBy:id,name','updatedBy:id,name',
                'tipoDocumento:id,nombre,categoria_padre_id',
                'entidad:id,nombre_fiscal',
                'ejercicio:id,nombre',
                'estado:id,nombre',
            ])
            ->whereIn('id', $this->ids)
            ->get([
                'id','aso_id','tipo_documento_id','entidad_id','ejercicio_id','estado_documento',
                'direccion_documento','fecha_documento','numero_serie','ref_externa','nombre','descripcion',
                'archivo','alt_usr','mod_usr','created_at','updated_at','deleted_at',
            ])
            ->map(function ($r) {
                $rutaTipo = $r->tipoDocumento?->ruta ?? $r->tipoDocumento?->nombre;
                return [
                    'id' => $r->id,
                    'aso_id' => $r->aso_id,
                    'tipo_documento' => $rutaTipo,
                    'entidad' => $r->entidad?->nombre_fiscal,
                    'ejercicio' => $r->ejercicio?->nombre ?? $r->ejercicio?->id,
                    'estado' => $r->estado?->nombre,
                    'direccion' => $r->direccion_documento,
                    'fecha_documento' => $r->fecha_documento,
                    'numero_serie' => $r->numero_serie,
                    'ref_externa' => $r->ref_externa,
                    'nombre' => $r->nombre,
                    'descripcion' => $r->descripcion,
                    'archivo' => $r->archivo,
                    'creado_por' => $r->createdBy?->name,
                    'modificado_por' => $r->updatedBy?->name,
                    'created_at' => $r->created_at,
                    'updated_at' => $r->updated_at,
                    'deleted_at' => $r->deleted_at,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'ID','Asociación (ID)','Tipo de documento','Entidad','Ejercicio','Estado',
            'Dirección','Fecha documento','Nº serie','Ref. externa','Nombre','Descripción',
            'Archivo','Creado por','Modificado por','Fecha creación','Última modificación','Eliminado en',
        ];
    }
}