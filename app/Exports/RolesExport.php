<?php

namespace App\Exports;

use App\Models\Rol;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RolesExport implements FromCollection, WithHeadings
{
    public function __construct(private Collection $ids) {}

    public function collection(): Collection
    {
        return Rol::query()
            ->whereIn('id', $this->ids)
            ->get([
                'id', 'nombre',
            ])
            ->map(function ($rol) {
                return [
                    'id'     => $rol->id,
                    'nombre' => $rol->nombre,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'ID','Nombre',
        ];
    }
}