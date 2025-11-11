<?php

namespace App\Exports;

use App\Models\Currency;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CurrenciesExport implements FromCollection, WithHeadings
{
    public function __construct(private Collection $ids) {}

    public function collection(): Collection
    {
        return Currency::query()
            ->whereIn('id', $this->ids)
            ->get([
                'id', 'codigo_iso', 'nombre', 'nombre_en', 'simbolo',
            ])
            ->map(function ($r) {
                return [
                    'id'         => $r->id,
                    'codigo_iso' => $r->codigo_iso,
                    'nombre'     => $r->nombre,
                    'nombre_en'  => $r->nombre_en,
                    'simbolo'    => $r->simbolo,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'ID','Código ISO','Nombre','Nombre en inglés','Símbolo',
        ];
    }
}