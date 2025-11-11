<?php

namespace App\Exports;

use App\Models\SocioTipo;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SocioTiposExport implements FromCollection, WithHeadings
{
    public function __construct(private Collection $ids) {}

    public function collection(): Collection
    {
        return SocioTipo::query()
            ->whereIn('id', $this->ids)
            ->get([
                'id', 'nombre',
            ])
            ->map(function ($r) {
                return [
                    'id'     => $r->id,
                    'nombre' => $r->nombre,
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