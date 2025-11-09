<?php

namespace App\Exports;

use App\Models\CategoriaInventario;
use App\Models\Ubicacion;
use App\Models\LibroInventario;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LibroInventarioExport implements FromCollection, WithHeadings
{
    public function __construct(private Collection $ids) {}

    public function collection()
    {
        return LibroInventario::query()
            ->with([
                'aso:id,nombre',
                'categoria:id,nombre,categoria_padre_id',
                'ubicacion:id,nombre,categoria_padre_id',
                'createdBy:id,name',
                'updatedBy:id,name',
            ])
            ->whereIn('id', $this->ids)
            ->get()
            ->map(function ($r) {
                $catRuta = $this->ruta($r->categoria, 'categoria_padre_id', CategoriaInventario::class);
                $ubiRuta = $this->ruta($r->ubicacion, 'categoria_padre_id', Ubicacion::class);
                return [
                    'ID' => $r->id,
                    'Asociación' => $r->aso?->nombre,
                    'Fecha' => optional($r->fecha_adquisicion)->format('Y-m-d'),
                    'Categoría' => $catRuta,
                    'Ubicación' => $ubiRuta,
                    'Cantidad' => $r->cantidad,
                    'Importe' => $r->valor,
                    'Descripción' => $r->descripcion,
                    'Creado por' => $r->createdBy?->name,
                    'Modificado por' => $r->updatedBy?->name,
                    'Fecha creación' => optional($r->created_at)->format('Y-m-d H:i:s'),
                    'Última modificación' => optional($r->updated_at)->format('Y-m-d H:i:s'),
                    'Eliminado en' => optional($r->deleted_at)->format('Y-m-d H:i:s'),
                ];
            });
    }

    public function headings(): array
    {
        return [
            'ID','Asociación','Fecha','Categoría','Ubicación','Cantidad','Importe','Descripción',
            'Creado por','Modificado por','Fecha creación','Última modificación','Eliminado en',
        ];
    }

    private function ruta($nodo, string $parentKey, string $model): string
    {
        if (!$nodo) return '';
        $ruta = [$nodo->nombre];
        $guard = 0;
        while ($nodo && $nodo->{$parentKey}) {
            $nodo = $model::query()->select('id','nombre',$parentKey)->find($nodo->{$parentKey});
            if ($nodo) array_unshift($ruta, $nodo->nombre);
            if (++$guard > 50) break;
        }
        return implode(' - ', $ruta);
    }
}