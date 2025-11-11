<?php

namespace App\Exports;

use App\Models\CategoriaContable;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CategoriaContableExport implements FromCollection, WithHeadings
{
    public function __construct(private Collection $ids) {}

    public function collection(): Collection
    {
        return CategoriaContable::query()
            ->with(['aso:id,nombre', 'padre:id,nombre'])
            ->whereIn('id', $this->ids)
            ->get([
                'id', 'aso_id', 'nombre', 'categoria_padre_id'
            ])
            ->map(function ($r) {
                return [
                    'id'              => $r->id,
                    'asociacion'      => $r->aso?->nombre,
                    'nombre'          => $r->nombre,
                    'categoria_padre' => $r->padre?->nombre
                ];
            });
    }

    public function headings(): array
    {
        return [
            'ID','Asociación','Nombre','Categoría padre'
        ];
    }
}