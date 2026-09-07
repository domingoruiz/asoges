<?php

namespace App\Pages;

use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Components\Select;
use App\Models\AsoUsr;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class SelectAso extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'pages.select-aso';
    protected static ?string $slug = 'select-aso';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $title = 'Seleccionar Asociación';

    public int|string|null $aso_id = null;

    public function mount(): void
    {
        session()->forget(['aso_actual', 'rol_activo', 'aso_label']);
    }

    public function hasOptions(): bool
    {
        return ! empty($this->getOptions());
    }

    protected function getFormSchema(): array
    {
        $options = $this->getOptions();

        return [
            Select::make('aso_id')
                ->label('Selecciona tu asociación')
                ->options($options)
                ->in(array_keys($options))
                ->required(),
        ];
    }

    protected function getOptions(): array
    {
        $user = Auth::user();
        if (! $user) {
            return [];
        }

        $opts = $user->asoUsuarios()
            ->with(['aso', 'rol'])
            ->get()
            ->sortBy(fn ($asoUsr) => $asoUsr->aso?->nombre)
            ->mapWithKeys(fn (AsoUsr $asoUsr) => [
                (string) $asoUsr->id => "{$asoUsr->aso?->nombre} ({$asoUsr->rol?->nombre})",
            ])
            ->toArray();

        if ($user->is_superadmin) {
            $opts['superadmin'] = 'Superadmin';
        }

        return $opts;
    }

    public function submit(): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }

        $options = $this->getOptions();
        $selected = $this->aso_id !== null ? (string) $this->aso_id : null;

        if ($selected === null || ! array_key_exists($selected, $options)) {
            $this->addError('aso_id', 'Selección no válida.');
            return;
        }

        if ($selected === 'superadmin') {
            if (! $user->is_superadmin) {
                $this->addError('aso_id', 'No tienes permisos de superadministrador.');
                return;
            }

            session([
                'aso_actual' => null,
                'rol_activo' => 'superadmin',
                'aso_label'  => 'Superadmin',
            ]);
        } else {
            $asoUsr = $user->asoUsuarios()->find($selected);
            if (! $asoUsr) {
                $this->addError('aso_id', 'No tienes acceso a esta asociación.');
                return;
            }

            session([
                'aso_actual' => $asoUsr->aso_id,
                'rol_activo' => $asoUsr->rol_id,
                'aso_label'  => $options[$selected] ?? null,
            ]);
        }

        session()->save();

        redirect(Dashboard::getUrl());
    }
}
