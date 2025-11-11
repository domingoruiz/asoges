<?php

namespace App\Exports;

use App\Models\LibroActa;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LibroActasExport implements FromQuery, WithMapping, WithHeadings
{
    public function __construct(private Collection $ids) {}

    public function query()
    {
        return LibroActa::query()
            ->with(['aso:id,nombre', 'tipoActa:id,nombre', 'estadoActa:id,nombre'])
            ->withCount('asistentes')
            ->whereIn('id', $this->ids);
    }

    public function map($a): array
    {
        return [
            $a->id,
            $a->aso?->nombre,
            $a->titulo,
            optional($a->fecha)->format('Y-m-d'),
            $a->hora_inicio,
            $a->hora_fin,
            $a->lugar_reunion,
            $a->tipoActa?->nombre,
            $a->estadoActa?->nombre,
            $a->aprobada ? 'Sí' : 'No',
            optional($a->fecha_aprobacion)->format('Y-m-d'),
            $a->asistentes_count,
        ];
    }

    public function headings(): array
    {
        return [
            'ID','Asociación','Título','Fecha','Hora inicio','Hora fin','Lugar',
            'Tipo de acta','Estado','Aprobada','Fecha aprobación','Nº asistentes',
        ];
    }
}