<?php

namespace App\Resources\Ejercicios\Pages;

use App\Resources\Ejercicios\EjercicioResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;
use App\Models\Ejercicio;

class CreateEjercicio extends CreateRecord
{
    protected static string $resource = EjercicioResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['aso_id'] = session('aso_actual');

        $overlaps = Ejercicio::query()
            ->where('aso_id', $data['aso_id'])
            ->where(function ($q) use ($data) {
                $q->whereBetween('fch_inicio', [$data['fch_inicio'], $data['fch_fin']])
                    ->orWhereBetween('fch_fin', [$data['fch_inicio'], $data['fch_fin']])
                    ->orWhere(function ($q2) use ($data) {
                        $q2->where('fch_inicio', '<=', $data['fch_inicio'])
                            ->where('fch_fin', '>=', $data['fch_fin']);
                    });
            })
            ->exists();

        if ($overlaps) {
            throw ValidationException::withMessages([
                'fch_inicio' => 'Las fechas se solapan con otro ejercicio de la misma asociación.',
                'fch_fin'    => 'Las fechas se solapan con otro ejercicio de la misma asociación.',
            ]);
        }

        return $data;
    }
}