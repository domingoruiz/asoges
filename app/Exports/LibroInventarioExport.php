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

    public function collection(): Collection
    {
        return LibroInventario::query()
            ->with(['aso:id,nombre', 'categoria:id,nombre,categoria_padre_id', 'ubicacion:id,nombre,categoria_padre_id'])
            ->whereIn('id', $this->ids)
            ->get([
                'id', 'aso_id', 'fecha_adquisicion', 'categoria_id', 'ubicacion_id', 'cantidad', 'valor', 'descripcion',
            ])
            ->map(function ($r) {
                $catRuta = $this->ruta($r->categoria, 'categoria_padre_id', CategoriaInventario::class);
                $ubiRuta = $this->ruta($r->ubicacion, 'categoria_padre_id', Ubicacion::class);

                return [
                    'id'          => $r->id,
                    'asociacion'  => $r->aso?->nombre,
                    'fecha'       => optional($r->fecha_adquisicion)->format('Y-m-d'),
                    'categoria'   => $catRuta,
                    'ubicacion'   => $ubiRuta,
                    'cantidad'    => $r->cantidad,
                    'importe'     => $r->valor,
                    'descripcion' => $r->descripcion,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'ID','Asociación','Fecha','Categoría','Ubicación',
            'Cantidad','Importe','Descripción',
        ];
    }

    private function ruta($nodo, string $parentKey, string $model): string
    {
        if (!$nodo) {
            return '';
        }

        $ruta = [$nodo->nombre];
        $guard = 0;

        while ($nodo && $nodo->{$parentKey}) {
            $nodo = $model::query()->select('id', 'nombre', $parentKey)->find($nodo->{$parentKey});
            if ($nodo) {
                array_unshift($ruta, $nodo->nombre);
            }
            if (++$guard > 50) {
                break;
            }
        }

        return implode(' - ', $ruta);
    }
}