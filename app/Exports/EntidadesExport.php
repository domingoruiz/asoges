<?php

namespace App\Exports;

use App\Models\Entidad;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EntidadesExport implements FromCollection, WithHeadings
{
    public function __construct(private Collection $ids) {}

    public function collection(): Collection
    {
        return Entidad::query()
            ->with(['aso:id,nombre', 'paisRel:id,nombre', 'continenteRel:id,nombre', 'monedaRel:id,codigo_iso,nombre,simbolo'])
            ->whereIn('id', $this->ids)
            ->get([
                'id', 'aso_id', 'nombre_fiscal', 'cif', 'direccion', 'cp', 'localidad', 'provincia',
                'pais', 'continente', 'telefono', 'email', 'web', 'swift_bic', 'iban', 'moneda', 'observaciones',
            ])
            ->map(function ($e) {
                return [
                    'id'            => $e->id,
                    'asociacion'    => $e->aso?->nombre,
                    'nombre_fiscal' => $e->nombre_fiscal,
                    'cif'           => $e->cif,
                    'direccion'     => $e->direccion,
                    'cp'            => $e->cp,
                    'localidad'     => $e->localidad,
                    'provincia'     => $e->provincia,
                    'pais'          => $e->paisRel?->nombre,
                    'continente'    => $e->continenteRel?->nombre,
                    'telefono'      => $e->telefono,
                    'email'         => $e->email,
                    'web'           => $e->web,
                    'swift_bic'     => $e->swift_bic,
                    'iban'          => $e->iban,
                    'moneda'        => $e->monedaRel?->codigo_iso,
                    'observaciones' => $e->observaciones,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'ID','Asociación','Nombre fiscal','CIF','Dirección','CP','Localidad','Provincia','País',
            'Continente','Teléfono','Email','Web','SWIFT/BIC','IBAN','Moneda','Observaciones',
        ];
    }
}