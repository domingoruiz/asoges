<?php

namespace App\Resources\EjercicioResource\Pages;

use App\Actions\AuditAction;
use App\Actions\FormActions;
use App\Resources\EjercicioResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\ValidationException;
use App\Models\Ejercicio;

class EditEjercicio extends EditRecord
{
    protected static string $resource = EjercicioResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['aso_id'] = $this->record->aso_id ?? session('aso_actual');

        $overlaps = Ejercicio::query()
            ->where('aso_id', $data['aso_id'])
            ->where('id', '!=', $this->record->id)
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

    protected function getFormActions(): array
    {
        return [
            FormActions::accept(),
            FormActions::cancel($this->getResource()::getUrl('index')),
            AuditAction::make()
        ];
    }
}