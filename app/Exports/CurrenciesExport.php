<?php

namespace App\Exports;

use App\Models\Currency;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CurrenciesExport implements FromCollection, WithHeadings
{
    private Collection $ids;

    public function __construct(Collection $ids)
    {
        $this->ids = $ids;
    }

    public function collection()
    {
        return Currency::query()
            ->whereIn('id', $this->ids)
            ->get([
                'id',
                'codigo_iso',
                'nombre',
                'nombre_en',
                'simbolo',
                'alt_usr',
                'mod_usr',
                'created_at',
                'updated_at',
            ]);
    }

    public function headings(): array
    {
        return [
            'ID',
            'Código ISO',
            'Nombre',
            'Nombre en inglés',
            'Símbolo',
            'Creado por (alt_usr)',
            'Modificado por (mod_usr)',
            'Fecha de creación',
            'Última modificación',
        ];
    }
}