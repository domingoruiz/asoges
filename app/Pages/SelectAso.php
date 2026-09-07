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

    protected function getFormSchema(): array
    {
        return [
            Select::make('aso_id')
                ->label('Selecciona tu asociación')
                ->options($this->getOptions())
                ->required(),
        ];
    }

    protected function getOptions(): array
    {
        $user = Auth::user();

        $opts = $user->asoUsuarios()
            ->with(['aso', 'rol'])
            ->get()
            ->sortBy(fn ($asoUsr) => $asoUsr->aso->nombre)
            ->mapWithKeys(fn (AsoUsr $asoUsr) => [
                $asoUsr->id => "{$asoUsr->aso->nombre} ({$asoUsr->rol->nombre})",
            ])
            ->toArray();

        if ($user->is_superadmin) {
            $opts['superadmin'] = 'Superadmin';
        }

        return $opts;
    }

    public function submit(): void
    {
        $selected = $this->aso_id;
        $options  = $this->getOptions();

        session([
            'aso_actual' => AsoUsr::find($selected)?->aso_id,
            'rol_activo' => $selected === 'superadmin' ? 'superadmin' : AsoUsr::find($selected)?->rol_id,
            'aso_label'  => $options[$selected] ?? null,
        ]);

        session()->save();

        redirect()->route('filament.asoges.pages..');
    }
}
