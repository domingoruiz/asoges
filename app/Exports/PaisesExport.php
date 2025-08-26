<?php

namespace App\Exports;

use App\Models\Pai;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PaisesExport implements FromCollection, WithHeadings
{
    public function __construct(private Collection $ids) {}

    public function collection()
    {
        return Pai::query()
            ->with(['continenteRel:id,nombre', 'monedaRel:id,codigo_iso,nombre,simbolo'])
            ->whereIn('id', $this->ids)
            ->get([
                'id','nombre','nombre_en','codigo_iso2','codigo_iso3','codigo_num','prefijo',
                'continente','moneda','alt_usr','mod_usr','created_at','updated_at',
            ])
            ->map(function ($pais) {
                return [
                    'id'            => $pais->id,
                    'nombre'        => $pais->nombre,
                    'nombre_en'     => $pais->nombre_en,
                    'iso2'          => strtoupper($pais->codigo_iso2),
                    'iso3'          => strtoupper($pais->codigo_iso3),
                    'codigo_pais'   => $pais->codigo_num,
                    'prefijo'       => $pais->prefijo,
                    'continente'    => $pais->continenteRel?->nombre,
                    'moneda'        => $pais->monedaRel?->nombre,
                    'moneda_iso'    => $pais->monedaRel?->codigo_iso,
                    'moneda_simbolo'=> $pais->monedaRel?->simbolo,
                    'alt_usr'       => $pais->alt_usr,
                    'mod_usr'       => $pais->mod_usr,
                    'created_at'    => $pais->created_at,
                    'updated_at'    => $pais->updated_at,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nombre',
            'Nombre en inglés',
            'ISO2',
            'ISO3',
            'Código país',
            'Prefijo',
            'Continente ID',
            'Continente',
            'Moneda ID',
            'Moneda',
            'Moneda ISO',
            'Símbolo',
            'Creado por (alt_usr)',
            'Modificado por (mod_usr)',
            'Fecha de creación',
            'Última modificación',
        ];
    }
}
