<?php

namespace App\Exports;

use App\Models\Aso;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AsosExport implements FromCollection, WithHeadings
{
    public function __construct(private Collection $ids) {}

    public function collection(): Collection
    {
        return Aso::query()
            ->whereIn('id', $this->ids)
            ->get([
                'id', 'fch_constitucion', 'nombre', 'cif', 'domicilio_social',
                'nro_registro', 'nro_registro_municipal', 'telefono', 'email', 'web',
            ])
            ->map(function ($aso) {
                return [
                    'id'                    => $aso->id,
                    'fch_constitucion'      => optional($aso->fch_constitucion)->format('Y-m-d'),
                    'nombre'                => $aso->nombre,
                    'cif'                   => $aso->cif,
                    'domicilio_social'      => $aso->domicilio_social,
                    'nro_registro'          => $aso->nro_registro,
                    'nro_registro_municipal'=> $aso->nro_registro_municipal,
                    'telefono'              => $aso->telefono,
                    'email'                 => $aso->email,
                    'web'                   => $aso->web,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'ID','Fecha constitución','Nombre','CIF','Domicilio social','Nº registro',
            'Nº registro municipal','Teléfono','Email','Web',
        ];
    }
}